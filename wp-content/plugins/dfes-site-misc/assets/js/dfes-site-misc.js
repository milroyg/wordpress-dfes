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

document.addEventListener('DOMContentLoaded', function () {
  document.body.addEventListener('click', function (e) {
    var link = e.target.closest('a'); // find clicked <a>

    if (!link) return; // not a link
    var href = link.getAttribute('href');
    if (!href) return; // no href

    // Skip mailto, tel, and specific YouTube link
    if (
      href.indexOf('mailto:') === 0 ||
      href.indexOf('tel:') === 0 ||
      href.indexOf('https://www.youtube.com/watch?v=HTWmLUyOk_k') === 0
    ) {
      return;
    }

    // Check if external
    if (link.hostname && link.hostname !== window.location.hostname) {
      var message = "This link will take you to an external website!\n\n" +
        "ही लिंक तुम्हाला बाह्य वेब साइटवर घेऊन जाईल!\n\n" +
        "ही लिंक तुमकां भायल्या संकेतथळार व्हरतली !";

      if (!confirm(message)) {
        e.preventDefault();
      }
    }
  }, false);
});

document.getElementById('feedback-float-btn').addEventListener('click', function () {

  const currentUrl = window.location.href;
  if(currentUrl.includes('/mr/')) {
    window.open('/mr/%e0%a4%b8%e0%a4%82%e0%a4%aa%e0%a4%b0%e0%a5%8d%e0%a4%95/', '_blank');
  } else if(currentUrl.includes('/kok/')) {
    window.open('/kok/%e0%a4%aa%e0%a5%8d%e0%a4%b0%e0%a4%a4%e0%a4%bf%e0%a4%95%e0%a5%8d%e0%a4%b0%e0%a4%bf%e0%a4%af%e0%a4%be/', '_blank');
  } else {
    window.open('/contact-us/', '_blank');
  }
});

// Google tag (gtag.js)
var script = document.createElement('script');
script.async = true;
script.src = 'https://www.googletagmanager.com/gtag/js?id=G-GXBX295WSD';
document.head.appendChild(script);

window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());

gtag('config', 'G-GXBX295WSD');