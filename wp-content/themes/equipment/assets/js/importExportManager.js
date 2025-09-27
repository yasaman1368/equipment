const EquipmentExcelManager = {
  elements: {},
  mode: "import",

  initialize() {
    this.cacheElements();
    this.attachEvents();
    this.populateFormSelector();
  },

  cacheElements() {
    this.elements = {
      modeSelect: document.getElementById("mode"),
      formSelect: document.getElementById("formsSelector"),
      downloadSection: document.getElementById("downloadBtnDiv"),
      selectedFormName: document.getElementById("formSelected"),
      uploadSection: document.getElementById("excelFileDiv"),
      actionButton: document.getElementById("modeBtn"),
      downloadTemplateButton: document.getElementById("excelFormat"),
      dlExcelFormatterBtn: document.getElementById("excelFormatDownloadBtn"),
    };
  },

  attachEvents() {
    this.elements.modeSelect.addEventListener("change", (e) =>
      this.updateModeUI(e)
    );
    this.elements.formSelect.addEventListener("change", (e) =>
      this.updateFormSelection(e)
    );
    this.elements.actionButton.addEventListener("click", () =>
      this.sendAction()
    );
    this.elements.dlExcelFormatterBtn.addEventListener("click", () =>
      this.downloadExcelFormat()
    );
  },

  async populateFormSelector() {
    try {
      const response = await ApiService.get("get_saved_forms");
      if (!response.success) return;

      const forms = response.data.forms || [];
      const { formSelect } = this.elements;

      // پاکسازی اولیه
      formSelect.innerHTML =
        "<option value='undefined'>فرم مورد نظر را انتخاب کنید</option>";

      // استفاده از DocumentFragment برای بهبود عملکرد
      const fragment = document.createDocumentFragment();
      forms.forEach(({ id, form_name }) => {
        const option = document.createElement("option");
        option.value = id;
        option.textContent = form_name;
        fragment.appendChild(option);
      });
      formSelect.appendChild(fragment);
    } catch (error) {
      console.error("Error loading forms:", error);
      const message =
        error?.response?.data?.message ||
        "خطایی در بارگذاری فرم‌ها رخ داده است";
      Notification.show("error", message);
    }
  },

  updateModeUI(e) {
    this.mode = e.target.value;
    const { actionButton, uploadSection, downloadSection } = this.elements;

    const modes = {
      export: {
        buttonText: "📑 خروجی اکسل",
        buttonMode: "export",
        showUpload: false,
        showDownload: false,
      },
      import: {
        buttonText: "📥 ثبت اطلاعات",
        buttonMode: "import",
        showUpload: true,
        showDownload: true,
      },
    };

    const config = modes[this.mode];
    actionButton.textContent = config.buttonText;
    actionButton.dataset.mode = config.buttonMode;

    uploadSection.classList.toggle("d-none", !config.showUpload);
    downloadSection.classList.toggle("d-none", !config.showDownload);
  },

  updateFormSelection(e) {
    const { value, options, selectedIndex } = e.target;
    const { selectedFormName, downloadSection } = this.elements;

    if (value !== "undefined") {
      selectedFormName.textContent = options[selectedIndex].text;
      if (this.mode === "import") {
        downloadSection.classList.remove("d-none");
      }
    } else {
      downloadSection.classList.add("d-none");
      selectedFormName.textContent = "";
    }
  },

  async sendAction() {
    const action = this.mode + "_equipments_data_from_form";
    const formId = this.elements.formSelect.value;

    if (formId === "undefined") {
      Notification.show("error", "یک فرم را انتخاب کنید");
      return;
    }
    if (this.mode === "export") {
      const form = document.createElement("form");
      form.method = "POST";
      const inputAction = makeHiddenInput("action", action);
      const inputFormId = makeHiddenInput("form_id", formId);

      form.append(inputAction, inputFormId);
      document.body.appendChild(form);
      form.submit();
      form.remove();
    }
    if (this.mode === "import") {
      this.importEquipmentData(formId);
    }
  },

  downloadExcelFormat() {
    const formId = this.elements.formSelect.value;
    const form = document.createElement("form");
    form.method = "POST";
    const inputAction = makeHiddenInput("action", "dlExcelFormatter");
    const inputFormId = makeHiddenInput("form_id", formId);

    form.append(inputAction, inputFormId);
    document.body.appendChild(form);
    form.submit();
    form.remove();
  },
  async importEquipmentData(formId) {
    const excelFile = document.getElementById("excelFile").files[0];
    if (!excelFile) {
      Notification.show("error", "فایل اکسل را انتخاب کنید");
    }
    const formData = new FormData();
    formData.append("form_id", formId);
    formData.append("excel_file", excelFile);
    try {
      const response = await ApiService.post(
        "import_equipments_data_from_form",
        formData
      );
    } catch (error) {}
  },
};

document.addEventListener("DOMContentLoaded", () => {
  if (window.location.pathname === "/panel/importexport")
    EquipmentExcelManager.initialize();
});
const makeHiddenInput = (name, value) => {
  const input = document.createElement("input");
  input.type = "hidden";
  input.name = name;
  input.value = value;
  return input;
};
