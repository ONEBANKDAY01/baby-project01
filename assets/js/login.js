document.addEventListener("DOMContentLoaded", function () {
  // เพิ่มลูกเล่นให้กับฟอร์ม
  const form = document.querySelector(".login-box");
  const inputs = document.querySelectorAll(".input-group input");

  // เพิ่มแอนิเมชันเคลื่อนไหวเมื่อผู้ใช้กรอกข้อมูล
  inputs.forEach((input) => {
    input.addEventListener("focus", function () {
      input.parentElement.classList.add("active"); // เพิ่มคลาส active เมื่อโฟกัส
    });
    input.addEventListener("blur", function () {
      if (!input.value) {
        input.parentElement.classList.remove("active"); // ลบคลาส active ถ้าไม่มีข้อมูล
      }
    });
  });

  // ฟังก์ชันการสมัคร
  const loginForm = document.getElementById("login-form");
  loginForm.addEventListener("submit", function (event) {
    event.preventDefault(); // ป้องกันการรีเฟรชหน้า
    validateForm();
  });

  function validateForm() {
    const username = document.getElementById("username").value;
    const password = document.getElementById("password").value;

    // ตรวจสอบข้อมูล
    if (username === "" || password === "") {
      showErrorMessage("Please fill in both fields.");
      shakeForm(); // สั่นฟอร์ม
    } else {
      // กำลังส่งข้อมูล
      showLoadingAnimation();
      setTimeout(() => {
        loginForm.submit();
      }, 1500);
    }
  }

  function showErrorMessage(message) {
    const errorDiv = document.querySelector(".error-message");
    errorDiv.textContent = message;
    errorDiv.style.opacity = 1;
    errorDiv.classList.add("fade-in");
  }

  function showLoadingAnimation() {
    const button = document.getElementById("login-btn");
    button.innerHTML = "Logging in... <span class='spinner'></span>";
    button.disabled = true;
  }

  function shakeForm() {
    const form = document.querySelector(".login-box");
    form.classList.add("shake");
    setTimeout(() => {
      form.classList.remove("shake");
    }, 500);
  }
});
