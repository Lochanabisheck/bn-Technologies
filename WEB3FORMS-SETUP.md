# Connect the appointment form to Web3Forms

The website already submits appointments to the official Web3Forms endpoint with JavaScript.

## 1. Create the access key

1. Open `https://web3forms.com`.
2. Enter the email address that should receive appointments.
3. Confirm the verification email from Web3Forms.
4. Copy the access key supplied by Web3Forms.

## 2. Add the key

Open `index.html` and find:

```html
<input type="hidden" name="access_key" value="YOUR_WEB3FORMS_ACCESS_KEY">
```

Replace only `YOUR_WEB3FORMS_ACCESS_KEY` with the real key.

## 3. Test delivery

Publish the site, submit a real appointment request, and confirm that:

1. The browser reaches `thank-you.html`.
2. The appointment arrives at the email connected to the access key.
3. Replying to the notification addresses the client's submitted email.

The access key is designed for browser-side forms. Use the Web3Forms dashboard's domain restriction options when available, and keep the included `botcheck` field enabled.
