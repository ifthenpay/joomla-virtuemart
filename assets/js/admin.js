jQuery().ready(function ($) {
  // safely assign language code
  var langCode = `en-GB`;
  waitAndSetLangToLangCode(lang);

  dom_hideCurrenciesBesides("47"); // hide other currencies besides euro

  dom_styleCallbackConfig(); // adds style to url of callback parent div

  // on config form submit
  Joomla.submitbutton = function (a) {
    form = document.getElementById("adminForm");
    var requiredField = form.querySelectorAll("[required]");
    var gatewayKey = form.querySelector("#params_gateway_key");
    var antiPhishingKey = form.querySelector("#anti_phishing_key");
    var minAmount = form.querySelector("#params_min_amount");
    var maxAmount = form.querySelector("#params_max_amount");
    var isValidate = true;

    // validate required fields
    [].forEach.call(requiredField, function (elm) {
      if (!$(elm).val()) {
        form.reportValidity();
        isValidate = false;
      }
    });

    dom_clearFormErrors();

    // validate gateway key
    if (!isgatewayKeyValid(gatewayKey.value)) {
      gatewayKey.classList.add("invalid");

      gatewayKey.insertAdjacentHTML(
        "afterend",
        `<p class="form_msg_error"> ${translateMsg(
          langCode,
          "is_invalid"
        )} </p>`
      );

      isValidate = false;
    }

    // validate anti-phishing key
    if (!isAntiPhishingKeyValid(antiPhishingKey.value)) {
      antiPhishingKey.classList.add("invalid");

      antiPhishingKey.insertAdjacentHTML(
        "afterend",
        `<p class="form_msg_error"> ${translateMsg(
          langCode,
          "is_invalid"
        )} </p>`
      );

      isValidate = false;
    }

    // validate min max
    if (!isNumValid(minAmount.value) || !isNumValid(maxAmount.value)) {
      if (!isNumValid(minAmount.value)) {
        minAmount.classList.add("invalid");

        minAmount.insertAdjacentHTML(
          "afterend",
          `<p class="form_msg_error"> ${translateMsg(
            langCode,
            "is_invalid"
          )} </p>`
        );

        isValidate = false;
      }
      if (!isNumValid(maxAmount.value)) {
        maxAmount.classList.add("invalid");

        maxAmount.insertAdjacentHTML(
          "afterend",
          `<p class="form_msg_error"> ${translateMsg(
            langCode,
            "is_invalid"
          )} </p>`
        );

        isValidate = false;
      }
    } else if (!isMinMaxValid(minAmount.value, maxAmount.value)) {
      // validate min max order amount
      minAmount.classList.add("invalid");
      maxAmount.classList.add("invalid");

      minAmount.insertAdjacentHTML(
        "afterend",
        `<p class="form_msg_error"> ${translateMsg(
          langCode,
          "is_invalid"
        )} </p>`
      );
      maxAmount.insertAdjacentHTML(
        "afterend",
        `<p class="form_msg_error"> ${translateMsg(
          langCode,
          "is_invalid"
        )} </p>`
      );

      isValidate = false;
    }

    if (isValidate) {
      form.task.value = a;
      form.submit();
      let callbackParams = getFormCallbackParams();
      ajax_postActivateCallback(callbackParams);
    }

    return false;
  };
});

function isNumValid(value) {
  let result = true;

  // only numbers and dot or empty
  if (value !== "") {
    let pattern = /^\$?[\d,]+(\.\d*)?$/;
    if (!value.match(pattern)) {
      result = false;
    }
  }

  return result;
}

function isMinMaxValid(min, max) {
  let result = true;
  if (min !== "" && max !== "") {
    if (min >= max) {
      result = false;
    }
  }

  return result;
}

function dom_clearFormErrors() {
  let msgs = document.querySelectorAll(".form_msg_error");

  if (msgs.length > 0) {
    msgs.forEach((msg) => {
      msg.remove();
    });
  }

  let invalids = document.querySelectorAll(".invalid");

  if (invalids.length > 0) {
    invalids.forEach((invalid) => {
      invalid.classList.remove("invalid");
    });
  }
}

/**
 * every 1 second (5 times) it verifies the lang variable to see if it is set, and when it is sets the new value and exits
 * @param {string} lang
 */
function waitAndSetLangToLangCode(lang) {
  for (let i = 0; i < 5; i++) {
    setTimeout(function () {
      if (typeof lang !== "undefined") {
        langCode = lang;
      }
    }, 1000);
  }
}

/**
 * Validate gateway key
 * @param {string} value
 * @returns {boolean} result
 */
function isgatewayKeyValid(value) {
  let result = true;

  // length
  if (value.length != 11) {
    result = false;
  }

  // characters
  let pattern = /([A-Z]{4}-\d{6}){1}/;
  if (!value.match(pattern)) {
    result = false;
  }

  return result;
}

/**
 * Validate anti-phishing key
 * @param {string} value
 * @returns {boolean} result
 */
function isAntiPhishingKeyValid(value) {
  let result = true;

  // length
  if (value.length < 10 || value.length > 50) {
    result = false;
  }

  // characters
  let pattern = /[a-zA-Z0-9]/;
  if (!value.match(pattern)) {
    result = false;
  }

  return result;
}

/**
 * Translates a string token into the corresponding language
 * This aproach was necessary in order to have multilanguage in the form error messages
 *
 * @param {string} langCode
 * @param {string} msg
 * @returns {string} msg
 */
function translateMsg(langCode, msg) {
  if (msg === `is_invalid`) {
    switch (langCode) {
      case `pt-PT`:
        msg = `Campo está inválido`;
        break;
      case `en-GB`:
        msg = `Field is invalid`;
        break;
      default:
        msg = `Field is invalid`;
    }
    return msg;
  }

  if (msg === `is_required`) {
    switch (langCode) {
      case `pt-PT`:
        msg = `Campo é obrigatório`;
        break;
      case `en-GB`:
        msg = `Field is required`;
        break;
      default:
        msg = `Field is required`;
    }
    return msg;
  }
}

/**
 * DOM - hides other currencies besides the one in the parameter
 * @param {string} currency
 */
function dom_hideCurrenciesBesides(currency) {
  let select = document.getElementById("params_payment_currency");
  let options = Array.from(select.options);

  if (options.length > 0) {
    options.forEach((option) => {
      if (option.value != currency) {
        option.remove();
      }
    });
  }
}

/**
 * DOM - add style to url callback parent div, since its not directly accessible
 */
function dom_styleCallbackConfig() {
  let callback = document.getElementById("callback");

  if (callback) {
    callback.parentElement.classList.add("url_callback");
  }
}
