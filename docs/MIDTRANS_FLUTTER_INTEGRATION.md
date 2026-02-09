# Midtrans Payment – Flutter Integration

Short guide for integrating the existing backend Midtrans Snap flow in your Flutter app.

---

## Flow overview

1. User chooses “Pay with Midtrans” for an order.
2. App calls backend to **initiate** payment and gets a **redirect URL**.
3. App opens that URL in a **WebView** (or in-app browser).
4. User completes payment on Midtrans’ page.
5. Midtrans redirects to our **return URL**; app detects it and shows the result.

---

## 1. Initiate payment

**Request**

```http
POST /api/payments/midtrans/initiate
Authorization: Bearer <user_token>
Content-Type: application/json

{
  "order_id": 123
}
```

**Success (200)**

```json
{
  "success": true,
  "redirect_url": "https://app.sandbox.midtrans.com/snap/v2/vtweb/<token>",
  "payment_reference": "<snap_token>"
}
```

**Error (400)**

```json
{
  "success": false,
  "message": "Error description from backend"
}
```

- Use `redirect_url` to open the payment page in a WebView.
- Keep `payment_reference` only if you need it for logging or support.

---

## 2. Show payment page

- Open `redirect_url` in a **WebView** (e.g. `webview_flutter`).
- Use a full-screen WebView or bottom sheet so the user stays in the app.
- Optional: show a loading state until the first URL has finished loading.

---

## 3. Handle return (payment done or cancelled)

Midtrans redirects the WebView to our **return URL** when the user finishes or closes the payment.  
Configure this in **Midtrans Dashboard → Settings → Configuration** as the **Payment Redirect URL** (or Snap Finish URL):

```text
https://<your-api-domain>/api/payments/midtrans/return
```

When the user is redirected there, the backend returns **JSON** (not HTML). So in Flutter you can:

**Option A – Intercept URL and call API**

- In the WebView’s navigation delegate, check if the current URL is your return URL (e.g. contains `/api/payments/midtrans/return`).
- When it matches:
  - Stop loading the WebView (or don’t load that URL in the WebView).
  - Call the same URL with `GET` from Dart (with the same query string if any), or reuse the query string from the intercepted URL.
  - Parse the JSON response and close the WebView.

**Option B – Read JSON from WebView**

- If the WebView loads the return URL and the backend responds with JSON, you can try to get the response body via your WebView API (if supported) and parse it.
- Then close the WebView and show success/failure.

**Return URL response (JSON)**

```json
{
  "status": "success",
  "order_id": "123",
  "payment_reference": "...",
  "message": "Payment completed successfully"
}
```

`status` can be: `success`, `pending`, `failed`, `cancelled`, `expired`.

- **success** – Payment completed; update order state and show success.
- **pending** – Payment not yet confirmed; show “waiting for payment” and optionally poll order status.
- **failed** / **cancelled** / **expired** – Show failure and let the user retry or go back.

---

## 4. Base URL and auth

- **Base URL:** Use your backend base URL (e.g. `https://api.yourapp.com` or `http://10.0.2.2:8000` for Android emulator).
- **Auth:** Send the user’s Bearer token in the `Authorization` header for `POST /api/payments/midtrans/initiate`.

---

## 5. Checklist

| Step | Action |
|------|--------|
| 1 | Call `POST /api/payments/midtrans/initiate` with `order_id` and Bearer token. |
| 2 | On success, open `redirect_url` in a WebView. |
| 3 | Set Midtrans Redirect/Finish URL to `https://<api>/api/payments/midtrans/return`. |
| 4 | In WebView, intercept or handle load to the return URL and parse the JSON `status` and `order_id`. |
| 5 | Close WebView and show success / pending / failure based on `status`. |

---

## 6. Notes

- **Webhook:** The backend already has a webhook for Midtrans. Payment status is updated server-side; your app can rely on the return URL for immediate UX and optionally refresh order details afterwards.
- **Sandbox:** Use Midtrans sandbox for testing; the backend is configured via env (e.g. `MIDTRANS_BASE_URL`, `MIDTRANS_SERVER_KEY`).
- **Deep link (optional):** If you prefer, the backend can redirect the return URL to a deep link (e.g. `yourapp://payment/return?order_id=123&status=success`) so the app opens automatically; that would require a small backend change to return a 302 to that URL instead of JSON.

If you want the deep-link redirect or different return response format, we can adjust the backend accordingly.
