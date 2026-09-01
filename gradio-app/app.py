import gradio as gr
import random
import requests
import json
import os
from datetime import datetime
from apscheduler.schedulers.background import BackgroundScheduler

# ── Configuration ────────────────────────────────────────────────────────────
SITE_URL = os.environ.get("SITE_URL", "https://buddhaword-web.hf.space")
SUTRA_API_URL = os.environ.get(
    "SUTRA_API_URL",
    "https://sheets.googleapis.com/v4/spreadsheets/"
    "1mKtgmZ_Is4e6P3P5lvOwIplqx7VQ3amicgienGN9zwA/values/Sheet1!1:1000000"
    "?key=AIzaSyDFjIl-SEHUsgK0sjMm7x0awpf8tTEPQjs",
)
LOG_FILE = "sent_log.json"

# ── Helpers ──────────────────────────────────────────────────────────────────

def load_log():
    if os.path.exists(LOG_FILE):
        with open(LOG_FILE, "r") as f:
            return json.load(f)
    return []


def save_log(log):
    with open(LOG_FILE, "w") as f:
        json.dump(log[-100:], f, ensure_ascii=False, indent=2)


def fetch_sutras():
    """Fetch sutra list from Google Sheets API (same source as the PHP app)."""
    try:
        resp = requests.get(SUTRA_API_URL, timeout=30)
        resp.raise_for_status()
        data = resp.json()
        return _transform(data)
    except Exception as e:
        print(f"[ERROR] fetch_sutras: {e}")
        return []


def _transform(api_response):
    """Google Sheets → list[dict], same logic as Sutra::transformData."""
    rows = api_response.get("values", [])
    if len(rows) < 2:
        return []
    headers = rows[0]
    result = []
    for row in rows[1:]:
        obj = {}
        for i, h in enumerate(headers):
            obj[h] = row[i].strip() if i < len(row) else ""
        obj.setdefault("ສຽງ", "")
        if any(obj.values()):
            result.append(obj)
    return result


def send_push(title: str, body: str, url: str) -> dict:
    """Call the PHP backend's /api/notify/send endpoint."""
    try:
        resp = requests.post(
            f"{SITE_URL}/api/notify/send",
            json={"title": title, "body": body, "url": url},
            timeout=30,
        )
        resp.raise_for_status()
        return resp.json()
    except Exception as e:
        return {"success": False, "error": str(e)}


# ── Core: pick random sutra → push ───────────────────────────────────────────

def auto_send_random_sutra() -> dict:
    sutras = fetch_sutras()
    if not sutras:
        return {"success": False, "error": "No sutras found"}

    sutra = random.choice(sutras)
    sutra_id = sutra.get("ID", "")
    title = sutra.get("ຊື່ພຣະສູດ", "ຄຳສອນພຸດທະ")
    url = f"{SITE_URL}/sutra/details/{sutra_id}"

    result = send_push(
        title=f"ພຣະສູດມື້ນີ້: {title}",
        body="ກະລຸນາອ່ານພຣະສູດແນະນຳມື້ນີ້",
        url=url,
    )

    log = load_log()
    log.append(
        {
            "time": datetime.now().strftime("%Y-%m-%d %H:%M:%S"),
            "sutra": title,
            "url": url,
            "result": result,
        }
    )
    save_log(log)
    return result


# ── Scheduler: 12:30 AM & 8:30 PM (Asia/Vientiane) ──────────────────────────

scheduler = BackgroundScheduler(timezone="Asia/Vientiane")
scheduler.add_job(auto_send_random_sutra, "cron", hour=0, minute=30, id="morning")
scheduler.add_job(auto_send_random_sutra, "cron", hour=20, minute=30, id="evening")
scheduler.start()

# ── Gradio UI ────────────────────────────────────────────────────────────────

with gr.Blocks(title="Auto Sutra Push", theme=gr.themes.Soft()) as demo:
    gr.Markdown(
        f"## ແຈ້ງເຕືອນອັດຕະໂນມັດ - ຄຳສອນພຸດທະ\n"
        f"Sends a random sutra push notification at **12:30 AM** and **8:30 PM** (Vientiane time).\n\n"
        f"Target site: `{SITE_URL}`"
    )

    with gr.Row():
        with gr.Column(scale=1):
            send_btn = gr.Button(" Send Now ", variant="primary", size="lg")
            result_box = gr.JSON(label="Last Result")

        with gr.Column(scale=1):
            log_btn = gr.Button("Refresh Log", size="sm")
            log_box = gr.JSON(label="Sent Log (last 100)")

    # Wire up buttons
    send_btn.click(fn=auto_send_random_sutra, outputs=result_box, api_name="send")
    log_btn.click(fn=load_log, outputs=log_box)

    # Show log on load
    demo.load(fn=load_log, outputs=log_box)

if __name__ == "__main__":
    demo.launch(server_port=7860)
