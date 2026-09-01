---
title: Auto Sutra Push Notifications
emoji: 📿
colorFrom: yellow
colorTo: orange
sdk: gradio
sdk_version: 4.44.0
app_file: app.py
pinned: false
license: mit
---

# Auto Sutra Push Notifications

Sends a random Buddhist sutra push notification to all subscribers at **12:30 AM** and **8:30 PM** (Asia/Vientiane timezone).

## How it works

1. Fetches sutra data from the Google Sheets API (same source as the main site)
2. Randomly selects one sutra
3. Sends a push notification via the PHP backend's `/api/notify/send` endpoint
4. All subscribed devices receive the notification with a link to the sutra detail page

## Schedule

| Time (Vientiane) | Description |
|------------------|-------------|
| 12:30 AM | Morning sutra notification |
| 8:30 PM | Evening sutra notification |

## Manual trigger

Use the "Send Now" button on the Gradio interface to manually send a random sutra notification.
