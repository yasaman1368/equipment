const Workflow = {
  init() {
    this.bindEvent();
  },

  bindEvent() {
    document.addEventListener("DOMContentLoaded", this.notificationCount());
  },
  //show notifitions for user's
  notificationCount() {
    counterElement = document.querySelector("input[name=notificationCount]");
    holderNotificationsCount = document.querySelector("#notificationCount");
    numberNotification = counterElement.value;
    holderNotificationsCount.textContent = numberNotification;
  },
  //show equipment
  // approve equpment data
  // reject equipment data
};

// ======================
// INITIALIZATION
// ======================
document.addEventListener("DOMContentLoaded", () => {
  Workflow.init();
});
