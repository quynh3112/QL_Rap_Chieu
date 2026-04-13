let currentDate = new Date();
let schedules = {};

function formatDate(date) {
  const d = new Date(date);
  return d.getFullYear() + "-" +
    String(d.getMonth() + 1).padStart(2, '0') + "-" +
    String(d.getDate()).padStart(2, '0');
}
function getWeekDates(date) {
  let start = new Date(date);
  start.setDate(date.getDate() - date.getDay() + 1);

  let week = [];
  for (let i = 0; i < 7; i++) {
    let d = new Date(start);
    d.setDate(start.getDate() + i);
    week.push(d);
  }
  return week;
}
async function loadSchedules( account_id) {
  try {
    const res = await fetch(`../Controller/workScheduleController.php?account_id=${account_id}`);
    const data = await res.json();

    schedules = {};

    data.forEach(item => {
      const key = formatDate(item.work_date);

      if (!schedules[key]) {
        schedules[key] = [];
      }

      schedules[key].push({
        time: item.start_time + " - " + item.end_time,
        job: item.note || item.shift
      });
    });

    console.log("Schedules:", schedules); // debug

    renderAll();
  } catch (err) {
    console.error(err);
  }
}

function renderWeek() {
  const weekBar = document.getElementById("weekBar");
  weekBar.innerHTML = "";

  const week = getWeekDates(currentDate);

  week.forEach(day => {
    const key = formatDate(day);
    const hasData = schedules[key] && schedules[key].length > 0;

    const div = document.createElement("div");

    div.className = "p-3 rounded-xl text-center cursor-pointer " +
      (formatDate(day) === formatDate(currentDate)
        ? "bg-blue-500"
        : "bg-gray-800 hover:bg-gray-700");

    div.innerHTML = `
      <p>${day.toLocaleDateString('vi-VN', { weekday: 'short' })}</p>
      <p class="font-bold">${day.getDate()}</p>
      ${hasData ? "<span class='text-green-400 text-xs'>●</span>" : ""}
    `;

    div.onclick = () => {
      currentDate = new Date(day);
      renderAll();
    };

    weekBar.appendChild(div);
  });
}

function renderSchedule() {
  const key = formatDate(currentDate);
  const container = document.getElementById("schedule");
  container.innerHTML = "";

  const data = schedules[key] || [];

  if (data.length === 0) {
    container.innerHTML = "<p class='text-gray-400 text-center col-span-3'>Không có lịch</p>";
    return;
  }

  data.forEach(item => {
    const div = document.createElement("div");
    div.className = "bg-gray-800 p-4 rounded-2xl";
    div.innerHTML = `
      <h4 class="text-blue-400 font-bold">${item.time}</h4>
      <p>${item.job}</p>
    `;
    container.appendChild(div);
  });
}

function renderAll() {
  renderWeek();
  renderSchedule();
}

function prevWeek() {
  currentDate.setDate(currentDate.getDate() - 7);
  renderAll();
}

function nextWeek() {
  currentDate.setDate(currentDate.getDate() + 7);
  renderAll();
}

loadSchedules();