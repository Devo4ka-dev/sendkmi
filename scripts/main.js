document.addEventListener("DOMContentLoaded", function () {
  // Инициализация элементов формы
  const messageForm = document.getElementById("messageForm");
  const submitButton = document.getElementById("submitButton");
  const responseDiv = document.getElementById("response");

  // Проверка существования элементов (на случай, если HTML не загружен)
  if (!messageForm || !submitButton || !responseDiv) {
    console.error("Form elements not found!");
    return;
  }

  let isSubmitting = false;

  // Логирование инициализации
  console.log({
    type: "init",
    page: "message_form",
    timestamp: new Date().toISOString(),
    url: window.location.href,
  });

  // Обработка перезагрузки страницы
  window.addEventListener("pageshow", function (event) {
    if (event.persisted) resetForm();
  });

  // Основной обработчик отправки формы
  messageForm.addEventListener("submit", function (e) {
    e.preventDefault();
    if (isSubmitting) return;
    isSubmitting = true;

    // Получение данных формы
    const formData = new FormData(this);
    const message = formData.get("message");

    // Проверки на валидность сообщения
    if (!message || message.trim().length === 0) {
      handleError(new Error("Message field is empty"));
      finalizeSubmission();
      return;
    }

    // Логирование действия
    logAction("submit", "create_message", {
      message_length: message.length,
      message_preview:
        message.substring(0, 100) + (message.length > 100 ? "..." : ""),
    });

    // Подготовка формы к отправке
    prepareForSubmission();

    // Отправка запроса
    fetch(window.location.href, {
      method: "POST",
      headers: { "Content-Type": "text/plain" },
      body: message,
    })
      .then((response) => handleResponse(response))
      .then((data) => {
        handleSuccess(data);
        lockForm(); // Блокировка формы при успехе
      })
      .catch((error) => {
        handleError(error);
        finalizeSubmission(); // Разблокировка при ошибке
      });
  });

  // Блокировка формы после успешной отправки
  function lockForm() {
    Array.from(messageForm.elements).forEach((element) => {
      element.disabled = true;
    });
    messageForm.classList.add("locked");
    submitButton.disabled = true;
    isSubmitting = false;
  }

  // Обработка ответа сервера
  function handleResponse(response) {
    return response.text().then((rawResponse) => {
      // Очистка ответа от ненужных данных
      const cleanedResponse = rawResponse
        .replace(/<script\b[^>]*>([\s\S]*?)<\/script>/gm, "")
        .replace(/^\s+|\s+$/g, "")
        .replace(/^[^{[]*/, "");

      try {
        return JSON.parse(cleanedResponse);
      } catch (e) {
        console.error("JSON parse error:", cleanedResponse);
        throw new Error(`Invalid JSON response: ${e.message}`);
      }
    });
  }

  // Успех отправки
  function handleSuccess(data) {
    if (data?.success && data?.url) {
      const url = data.url;
      responseDiv.innerHTML = `Message saved successfully. <a href="${url}" class="copy-link" data-url="${url}">${url}</a>`;
      responseDiv.classList.add("visible");
      setupCopyButton();
    } else {
      handleError(new Error("Invalid server response"));
    }
  }

  // Обработка ошибок
  function handleError(error) {
    console.error("Error:", error);
    responseDiv.textContent = "Error: " + error.message;
    responseDiv.classList.add("visible");
  }

  // Настройка кнопки копирования
  function setupCopyButton() {
    const copyLink = responseDiv.querySelector(".copy-link");
    if (copyLink) {
      copyLink.addEventListener("click", function (e) {
        e.preventDefault();
        const url = this.getAttribute("data-url");
        navigator.clipboard.writeText(url).then(() => {
          showNotification("URL copied to clipboard");
        });
      });
    }
  }

  // Логирование действий
  function logAction(type, action, data = {}) {
    console.log({
      type,
      action,
      timestamp: new Date().toISOString(),
      data,
    });
  }

  // Уведомление пользователя
  function showNotification(message) {
    const notification = document.createElement("div");
    notification.className = "notification";
    notification.textContent = message;
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 2000);
  }

  // Подготовка к отправке
  function prepareForSubmission() {
    submitButton.disabled = true;
    messageForm.classList.add("sent");
    responseDiv.classList.remove("visible");
  }

  // Полный сброс формы
  function resetForm() {
    messageForm.reset();
    Array.from(messageForm.elements).forEach((element) => {
      element.disabled = false;
    });
    submitButton.disabled = false;
    messageForm.classList.remove("sent", "locked");
    responseDiv.textContent = "";
    responseDiv.classList.remove("visible");
    isSubmitting = false;
  }

  // Разблокировка при ошибках
  function finalizeSubmission() {
    Array.from(messageForm.elements).forEach((element) => {
      element.disabled = false;
    });
    submitButton.disabled = false;
    messageForm.classList.remove("sent");
    isSubmitting = false;
  }
});
