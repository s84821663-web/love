const params = new URLSearchParams(location.search);
let code = params.get('code');

let state = {
  food: '',
  date: '',
  time: ''
};

const steps = ['step1', 'step2', 'step3', 'step4', 'final'];
const dots = document.querySelectorAll('.dot');

function showStep(index) {
  document.querySelectorAll('.step').forEach(el => el.classList.remove('active'));
  document.getElementById(steps[index]).classList.add('active');

  dots.forEach((dot, i) => {
    dot.classList.toggle('active', i === Math.min(index, 3));
  });
}

function toast(message) {
  const el = document.getElementById('toast');
  el.textContent = message;
  el.classList.add('show');
  setTimeout(() => el.classList.remove('show'), 1800);
}

async function save(step, answer) {
  if (!code) return;

  try {
    await fetch('api/save-response.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({ code, step, answer })
    });
  } catch (e) {
    console.log(e);
  }
}

async function createInviteIfNeeded() {
  if (code) return;

  try {
    const res = await fetch('api/create-invite.php');
    const data = await res.json();

    if (data.success) {
      code = data.code;
      history.replaceState({}, '', '?code=' + code);
    }
  } catch (e) {
    toast('اتصال به سرور برقرار نشد');
  }
}

document.getElementById('yesBtn').addEventListener('click', async () => {
  await save('accepted', true);
  showStep(1);
});

document.getElementById('noBtn').addEventListener('mouseenter', moveNoButton);
document.getElementById('noBtn').addEventListener('click', moveNoButton);

function moveNoButton() {
  const btn = document.getElementById('noBtn');
  btn.style.position = 'relative';
  btn.style.left = (Math.random() * 120 - 60) + 'px';
  btn.style.top = (Math.random() * 60 - 30) + 'px';
}

document.querySelector('.next').addEventListener('click', () => showStep(2));

document.querySelectorAll('.choice').forEach(button => {
  button.addEventListener('click', async () => {
    state.food = button.dataset.food;
    await save('food', state.food);
    showStep(3);
  });
});

document.getElementById('finishBtn').addEventListener('click', async () => {
  const date = document.getElementById('dateInput').value;
  const time = document.getElementById('timeInput').value;

  if (!date || !time) {
    toast('تاریخ و ساعت رو انتخاب کن ❤️');
    return;
  }

  state.date = date;
  state.time = time;

  await save('date', state.date);
  await save('time', state.time);

  document.getElementById('finalFood').textContent = state.food;
  document.getElementById('finalDate').textContent = state.date;
  document.getElementById('finalTime').textContent = state.time;
  document.getElementById('finalText').textContent =
    'پس قرارمون ثبت شد! منتظرت می‌مونم 😍';

  showStep(4);
});

createInviteIfNeeded();
