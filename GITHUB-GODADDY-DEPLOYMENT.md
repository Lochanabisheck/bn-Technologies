# Publish BN Technologies with GitHub Pages and GoDaddy DNS

## 1. Add the Web3Forms key

Complete `WEB3FORMS-SETUP.md` before publishing. The form will show a configuration message until the placeholder access key in `index.html` is replaced.

## 2. Create the GitHub repository

1. Create a new GitHub repository.
2. Upload every file and folder from this website package to the repository root.
3. Confirm that `index.html`, `CNAME`, `styles.css`, `script.js`, and the `assets` folder are at the top level.
4. Commit the files to the `main` branch.

## 3. Enable GitHub Pages

1. Open the repository's **Settings → Pages**.
2. Under **Build and deployment**, choose **Deploy from a branch**.
3. Select the `main` branch and the `/ (root)` folder.
4. Save and wait for GitHub to publish the site.
5. Under **Custom domain**, enter `bntechnologies.ai` and save.

## 4. Point GoDaddy to GitHub Pages

Open **GoDaddy → Domains → bntechnologies.ai → DNS**. Remove conflicting parking or website-builder records, then add these four apex records:

| Type | Name | Value |
| --- | --- | --- |
| A | `@` | `185.199.108.153` |
| A | `@` | `185.199.109.153` |
| A | `@` | `185.199.110.153` |
| A | `@` | `185.199.111.153` |

Add the `www` record after replacing `YOUR-GITHUB-USERNAME` with the repository owner's GitHub username or organization:

| Type | Name | Value |
| --- | --- | --- |
| CNAME | `www` | `YOUR-GITHUB-USERNAME.github.io` |

Do not include the repository name in the `www` CNAME value. Do not use wildcard DNS records.

## 5. Enable HTTPS

Return to **GitHub → Repository Settings → Pages**. When the certificate is ready, enable **Enforce HTTPS**. DNS changes can take up to 24 hours to propagate, and the HTTPS option may also take time to become available.

## 6. Launch checks

- Open both `https://bntechnologies.ai` and `https://www.bntechnologies.ai`.
- Test the website on desktop and mobile.
- Submit a real appointment and verify email delivery.
- Confirm the success page appears after submission.
- Verify that no old parking page or conflicting DNS record remains.
