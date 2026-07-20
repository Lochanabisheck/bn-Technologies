# Publish bntechnologies.ai on GoDaddy

## 1. Prepare GoDaddy hosting

Use a GoDaddy Linux Web Hosting plan with PHP support. A domain registration by itself does not run the PHP appointment handler.

In the GoDaddy dashboard, open **My Products → Web Hosting → Manage** and launch the cPanel or hosting file manager.

## 2. Create domain email addresses

Create or connect these mailboxes before launch:

- `appointments@bntechnologies.ai`: receives booking requests
- `website@bntechnologies.ai`: sender used by the website

If a different appointment address is preferred, edit `api/config.php` and the visible email links in `index.html`, `thank-you.html`, and `booking-error.html`.

## 3. Upload the website

Upload the contents of the `bn-technologies` folder into the hosting document root, normally `public_html`.

The result should look like this:

```text
public_html/
  index.html
  styles.css
  script.js
  .htaccess
  assets/
  api/
```

Do not upload the enclosing folder unless the domain document root is specifically pointed to it.

## 4. Connect the domain

When the domain and hosting are in the same GoDaddy account, use GoDaddy's **Connect Domain** action. Otherwise, open **Domains → bntechnologies.ai → DNS** and set:

| Type | Name | Value |
| --- | --- | --- |
| A | `@` | GoDaddy hosting IPv4 address |
| CNAME | `www` | `@` |

Remove conflicting `@` A records or `www` CNAME records. DNS changes can take up to 48 hours, although they are often visible sooner.

## 5. Enable HTTPS

Open the hosting security or SSL section and enable the SSL certificate for both `bntechnologies.ai` and `www.bntechnologies.ai`. Wait until the certificate is active, then confirm both addresses load over `https://` without warnings.

## 6. Confirm PHP email delivery

Open `api/config.php` in the file manager and verify the destination and sender mailboxes. Submit a real test booking from the live HTTPS website.

Check that:

1. The browser reaches `thank-you.html`.
2. The message arrives at the appointment inbox.
3. Replying to the message addresses the client's email.
4. The message does not land in spam.

If the form reaches `booking-error.html`, confirm that PHP `mail()` is enabled for the hosting plan and that `website@bntechnologies.ai` is a valid mailbox on the hosted domain. Ask GoDaddy support to confirm outbound PHP mail for the account if needed.

## 7. Improve email trust

In GoDaddy DNS, enable the SPF and DKIM records provided by the email service. Add a DMARC record after SPF and DKIM are working. This reduces the chance that appointment notifications are classified as spam.

## 8. Final launch checks

- Test desktop and mobile layouts.
- Submit bookings for multiple topics and dates.
- Confirm weekend dates are rejected.
- Confirm the official Beldex links open in a new tab.
- Verify the independent-consultancy and risk notices remain visible.
- Submit `https://bntechnologies.ai/sitemap.xml` to Google Search Console.
