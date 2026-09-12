jQuery(document).bind('contextmenu', function (e) {
  e.preventDefault(); // Prevent the default context menu from appearing
  return false;
});

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

jQuery(document).ready(function ($) {
  $(document).on('click', 'a', function (e) {
    var link = $(this);
    if (link.length && link[0].hostname !== window.location.hostname) {
      var confirmLeave = confirm('You are about to leave this site. Continue?');
      if (!confirmLeave) {
        e.preventDefault();
      }
    }
  });
});
