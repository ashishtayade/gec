 // JavaScript Document
 /* initialize validation */
 $('form').validate({
     ignore: [],
     onkeyup: function(element) {
         $(element).valid()
     },
     errorClass: 'help-block',
     errorElement: 'span',
     errorPlacement: function(error, e) {
         e.closest('.validateField').append(error);
         return false;
     },
     highlight: function(e) {
         console.log('validation error');
         $(e).closest('.validateField').removeClass('has-success has-error').addClass('has-error');
         $(e).closest('.help-block').remove();
         return false;
     },
     success: function(e) {
         console.log('validation success');
         // You can remove the .addClass('has-success') part if you don't want the inputs to get green after success!
         e.closest('.validateField').removeClass('has-success has-error').addClass('has-success');
         e.closest('.help-block').remove();
         return false;
     }
 });

 /* add class based validation rules */
 $.validator.addClassRules({
     validateRequired: {
         required: true
     },
     validateAlphaonly: {
         alphaOnly: true
     },
     validateNumber: {
         digits: true
     },
     validateDecimalNumber: {
         number: true
     },
     validateEmail: {
         validateEmail: true
     },
     validateURL: {
         url: true
     },
     validatePassword: {
         passwordCheck: true
     },
     validateConfirmPassword: {
         equalTo: '.validatePassword'
     },
     validateMobileNoLimit: {
         minlength: 10,
         maxlength: 10,
         digits: true
     }
 });

 //addMethod
 //password validation (This will match 1 or more uppercase characters, 1 or more lowercase characters and 1 or more digit/special character) and at least 8 char
 $.validator.addMethod("passwordCheck", function(value) {
     return /[A-Z]+/.test(value) && /[a-z]+/.test(value) &&
         /[\d\W]+/.test(value) && /\S{8,}/.test(value);
 }, 'Must contain minimum 8 characters including 1 Upper, 1 Lower and 1 or more digit/special char');

 $.validator.addMethod("validateEmail", function(value, element) {
     // allow any non-whitespace characters as the host part
     return this.optional(element) || /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(value);
 }, 'Please enter a valid email address.');

 // regx for alphabet and space
 $.validator.addMethod("alphaOnly", function(value, element) {
     return this.optional(element) || /^[a-z\s]+$/i.test(value);
 }, "Must contain alphabet characters only.");

 /* function for validate form field on submit click */
 function ValidateAndSubmit(evt) {
     var $group = $(evt.currentTarget).parents('.validateContainer');
     var isValid = true;
     $group.find('.validateField .form-control').each(function(i, item) {
         if (!$(item).valid())
             isValid = false;
     });
     if (!isValid)
         evt.preventDefault();
 }

 /* call 'ValidateAndSubmit' function */
 $('.validateContainer .checkValidationBtn').click(ValidateAndSubmit);