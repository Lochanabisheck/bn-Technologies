const header = document.querySelector('.site-header');
const menuButton = document.querySelector('.menu-toggle');
const navigation = document.querySelector('.site-nav');
const navigationLinks = navigation.querySelectorAll('a');
const meetingDate = document.querySelector('#meeting-date');
const messageField = document.querySelector('textarea[name="message"]');
const characterCount = document.querySelector('#char-count');
const bookingForm = document.querySelector('#booking-form');
const formStatus = document.querySelector('#form-status');
const officialLogo = document.querySelector('.official-mark img');

const updateHeader = () => header.classList.toggle('is-scrolled', window.scrollY > 24);
updateHeader();
window.addEventListener('scroll', updateHeader, { passive: true });

menuButton.addEventListener('click', () => {
  const isOpen = menuButton.getAttribute('aria-expanded') === 'true';
  menuButton.setAttribute('aria-expanded', String(!isOpen));
  navigation.classList.toggle('is-open', !isOpen);
  document.body.classList.toggle('menu-open', !isOpen);
});

navigationLinks.forEach((link) => {
  link.addEventListener('click', () => {
    menuButton.setAttribute('aria-expanded', 'false');
    navigation.classList.remove('is-open');
    document.body.classList.remove('menu-open');
  });
});

document.querySelectorAll('.accordion-item button').forEach((button) => {
  button.addEventListener('click', () => {
    const item = button.closest('.accordion-item');
    const shouldOpen = !item.classList.contains('is-open');
    document.querySelectorAll('.accordion-item').forEach((accordionItem) => {
      accordionItem.classList.remove('is-open');
      accordionItem.querySelector('button').setAttribute('aria-expanded', 'false');
      accordionItem.querySelector('button i').textContent = '+';
    });
    if (shouldOpen) {
      item.classList.add('is-open');
      button.setAttribute('aria-expanded', 'true');
      button.querySelector('i').textContent = '−';
    }
  });
});

const revealObserver = new IntersectionObserver((entries) => {
  entries.forEach((entry) => {
    if (entry.isIntersecting) {
      entry.target.classList.add('is-visible');
      revealObserver.unobserve(entry.target);
    }
  });
}, { threshold: 0.12 });

document.querySelectorAll('.reveal').forEach((element) => revealObserver.observe(element));

if (officialLogo.complete) {
  officialLogo.closest('.official-mark').classList.add(officialLogo.naturalWidth > 0 ? 'logo-loaded' : 'logo-failed');
} else {
  officialLogo.addEventListener('load', () => officialLogo.closest('.official-mark').classList.add('logo-loaded'));
  officialLogo.addEventListener('error', () => officialLogo.closest('.official-mark').classList.add('logo-failed'));
}

const earliestDate = new Date();
earliestDate.setDate(earliestDate.getDate() + 1);
const latestDate = new Date();
latestDate.setDate(latestDate.getDate() + 180);
const formatDateInput = (date) => {
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  return `${year}-${month}-${day}`;
};
meetingDate.min = formatDateInput(earliestDate);
meetingDate.max = formatDateInput(latestDate);

meetingDate.addEventListener('change', () => {
  const selectedDate = new Date(`${meetingDate.value}T12:00:00`);
  const isWeekend = selectedDate.getDay() === 0 || selectedDate.getDay() === 6;
  meetingDate.setCustomValidity(isWeekend ? 'Please choose a Monday to Friday appointment date.' : '');
  formStatus.textContent = isWeekend ? 'Appointments are available Monday to Friday.' : '';
});

messageField.addEventListener('input', () => {
  characterCount.textContent = String(messageField.value.length);
});

bookingForm.addEventListener('submit', (event) => {
  if (!bookingForm.checkValidity()) {
    event.preventDefault();
    formStatus.textContent = 'Please complete all required fields before submitting.';
    bookingForm.reportValidity();
    return;
  }

  const selectedDate = new Date(`${meetingDate.value}T12:00:00`);
  const isWeekend = selectedDate.getDay() === 0 || selectedDate.getDay() === 6;
  if (isWeekend) {
    event.preventDefault();
    formStatus.textContent = 'Appointments are available Monday to Friday.';
    meetingDate.focus();
    return;
  }
  if (selectedDate < new Date(`${meetingDate.min}T00:00:00`)) {
    event.preventDefault();
    formStatus.textContent = 'Please choose a future appointment date.';
    meetingDate.focus();
    return;
  }

  const submitButton = bookingForm.querySelector('.submit-button');
  submitButton.classList.add('is-loading');
  submitButton.querySelector('span').textContent = 'Sending request…';
  formStatus.textContent = '';
});

document.querySelector('#current-year').textContent = String(new Date().getFullYear());
