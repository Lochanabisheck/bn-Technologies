const form = document.querySelector('#appointment-form');
const status = document.querySelector('.form-status');
const menuButton = document.querySelector('.menu-toggle');
const navigation = document.querySelector('.desktop-nav');

document.querySelector('#year').textContent = new Date().getFullYear();

menuButton?.addEventListener('click', () => {
  const isOpen = navigation.classList.toggle('open');
  menuButton.setAttribute('aria-expanded', String(isOpen));
});

navigation?.querySelectorAll('a').forEach((link) => link.addEventListener('click', () => {
  navigation.classList.remove('open');
  menuButton?.setAttribute('aria-expanded', 'false');
}));

form?.addEventListener('submit', async (event) => {
  event.preventDefault();
  if (!form.checkValidity()) {
    form.reportValidity();
    return;
  }

  const button = form.querySelector('.submit-button');
  const accessKey = form.querySelector('[name="access_key"]').value;
  if (accessKey === 'YOUR_WEB3FORMS_ACCESS_KEY') {
    status.textContent = 'Add your Web3Forms access key in script setup to activate email delivery.';
    status.className = 'form-status error';
    return;
  }

  button.disabled = true;
  button.querySelector('span').textContent = 'Sending request...';
  status.textContent = '';
  status.className = 'form-status';

  try {
    const response = await fetch('https://api.web3forms.com/submit', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
      body: JSON.stringify(Object.fromEntries(new FormData(form)))
    });
    const result = await response.json();
    if (!result.success) throw new Error(result.message || 'Unable to send your request.');
    status.textContent = 'Thank you — your appointment request is on its way. We’ll be in touch soon.';
    status.className = 'form-status success';
    form.reset();
  } catch (error) {
    status.textContent = error.message || 'Something went wrong. Please email hello@bntechnologies.ai.';
    status.className = 'form-status error';
  } finally {
    button.disabled = false;
    button.querySelector('span').textContent = 'Request an appointment';
  }
});
