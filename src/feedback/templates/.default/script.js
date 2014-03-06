
/*
 * This file is part of the Studio Fact package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
'use strict';

var Feedback = function(form) {
	this.form = $(form);
	this.currentUri = window.location.href;
}

Feedback.prototype.getData = function(action) {		
	var data = this.form.serializeArray();
	switch (action) {
		case 'feedback_remote':
			data.push({ name: 'feedback_remote', value: 'true' });
			data.push({ name: this.form.find('.submit-form').attr('name'), value: 'true' });
			data = $.param(data);
			break;
		
		case 'feedback_captcha_remote':
			data.push({ name: 'feedback_captcha_remote', value: 'true' });
			data = $.param(data);
			break;
			
		default:
			data = this.form.serialize();
			break;
	}
	
	return data;
}

Feedback.prototype.submitForm = function (callback) {
	var self = this;
	$.post(this.currentUri, this.getData('feedback_remote'), function(response) {
		self.setCaptcha(response.captcha);
		if (typeof(callback) == 'function') {
			callback(response);
		}
	});
}

Feedback.prototype.reloadCaptcha = function() {
	var self = this;
	$.post(this.currentUri, this.getData('feedback_captcha_remote'), function(response) {
		self.setCaptcha(response.captcha);
	});
}

Feedback.prototype.setCaptcha = function(code) {
	this.form.find('input[name*=captcha_sid]').val(code);
	this.form.find('.captcha-image').prop('src', '/bitrix/tools/captcha.php?captcha_sid=' + code);
}