jQuery(document).ready(function ($) {
  const AES_KEY = "my32charsecretkeymy32charsecretkey"; // 🔑 Must match the server-side decryption key

  $("#loginform").on("submit", function () {
    const $userField = $("#user_login");
    const $passField = $("#user_pass");

    if ($userField.length && $passField.length) {
      const encryptedUser = CryptoJS.AES.encrypt($userField.val(), AES_KEY).toString();
      const encryptedPass = CryptoJS.AES.encrypt($passField.val(), AES_KEY).toString();

      // Replace original values with encrypted strings
      $userField.val(encryptedUser);
      $passField.val(encryptedPass);
    }
  });
});
