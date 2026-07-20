# BN Technologies website

Production-ready static website with a PHP appointment email handler for `bntechnologies.ai`.

## Run locally

```bash
php -S 127.0.0.1:8080
```

Open `http://127.0.0.1:8080`.

The contact form uses PHP `mail()`. Local delivery only works when a mail transport is configured. The production setup is covered in `GODADDY-DEPLOYMENT.md`.

## Configure booking email

Edit `api/config.php` before publishing:

```php
$bookingRecipient = 'appointments@bntechnologies.ai';
$bookingSender = 'website@bntechnologies.ai';
```

Both addresses should exist on the domain. The recipient receives a formatted booking email and can reply directly to the client.

## Main files

- `index.html`: page content, links, and booking form
- `styles.css`: complete responsive design system
- `script.js`: mobile navigation, FAQ, animations, and form validation
- `api/book-appointment.php`: validation and booking email delivery
- `api/config.php`: destination and sender email addresses
- `GODADDY-DEPLOYMENT.md`: domain, hosting, SSL, and testing steps

## Beldex attribution

The website uses public factual references and direct links from the official Beldex website. The official Beldex logo is loaded from Beldex's media-kit URL. BN Technologies is clearly presented as an independent consultancy.
