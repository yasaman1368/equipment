function triggerExport(event) {
  // event.preventDefult()

  // Create a hidden form
  var form = document.createElement("form");
  form.method = "GET";
  form.action = "";

  // Add the action parameter
  var input = document.createElement("input");
  input.type = "hidden";
  input.name = "action";
  input.value = "export_equipments";
  form.appendChild(input);

  // Append the form to the body and submit it
  document.body.appendChild(form);
  form.submit();
}

function fetchData(url, body = {}) {
  const params = new URLSearchParams(body);
  return fetch(url, {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: params,
  }).then((response) => response.json());
}

function updateNotifications() {
  const countEl = document.getElementById("count-notification");
  const holderEl = document.querySelector("#notificationCount");
  const messageEl = document.getElementById("notification-messages");
  const iconEl = document.getElementById("top-icon-notification");

  if (!countEl || !holderEl) return; // اگه المان‌ها نبودن، هیچی اجرا نمیشه

  const count = parseInt(countEl.value, 10) || 0;

  if (count === 0) {
    holderEl.classList.add("d-none");
    if (messageEl) messageEl.textContent = "هیچ اعلانی برای شما وجود ندارد";
    if (iconEl) iconEl.classList.remove("hasNotification");
    return;
  }

  // وقتی اعلان وجود داره
  holderEl.classList.remove("d-none");
  if (messageEl) {
    messageEl.innerHTML = `<span class="text-danger">${count}</span> تجهیز نیازمند بررسی شماست`;
  }
  if (iconEl) {
    iconEl.classList.add("hasNotification", "text-center");
    iconEl.textContent = count;
  }
}

document.addEventListener("DOMContentLoaded", updateNotifications);
