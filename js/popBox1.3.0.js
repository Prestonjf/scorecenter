/*
* jQuery popBox
* Copyright (c) 2011 Simon Hibbard
* 
* Permission is hereby granted, free of charge, to any person
* obtaining a copy of this software and associated documentation
* files (the "Software"), to deal in the Software without
* restriction, including without limitation the rights to use,
* copy, modify, merge, publish, distribute, sublicense, and/or sell
* copies of the Software, and to permit persons to whom the
* Software is furnished to do so, subject to the following
* conditions:

* The above copyright notice and this permission notice shall be
* included in all copies or substantial portions of the Software.
* 
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
* EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
* OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
* NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
* HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
* WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
* FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
* OTHER DEALINGS IN THE SOFTWARE. 
*/

/*
* Version: V1.3.0
* Release: 26-01-2011
* Based on jQuery 1.5.0
* Additional features provided with thanks to Alex Lareau
*/

(function ($) {
    $.fn.popBox = function (options, type) {

        var defaults = {
            height: 100,
            width: 300,
            newlineString: "<br/>"
        };
        var options = $.extend(defaults, options);


        return this.each(function () {

            obj = $(this);

            var inputName = 'popBoxInput' + obj.attr("Id");
            var labelValue = $("label[for=" + obj.attr('id') + "]").text();
			
			if (type == 'copyPaste') {
            	obj.after('<div class="popBox-holder"></div><div class="popBox-container"><label><span style="font-weight:normal;font-size:14px;"><h4>Bulk Copy</h4>Copy values from a spreadsheet (in corresponding team order as this screen) <br />and paste them in the text field below. Do not include alternate teams. <br />Once finished, select done.</label><br /><label style="display: none;" for="' + inputName + '">' + labelValue + '</span></label><textarea id="' + inputName + '" name="' + inputName + '" class="popBox-input" /><div class="done-button"><input type="button" value="Done" class="btn btn-xs btn-primary"/></div></div>');
			}
			else if (type == 'about') {
			
				obj.after('<div class="popBox-holder"></div><div class="popBox-container"><label><span style="font-weight:normal;font-size:14px;"><h4>Tournament Score Center</h4><br />Developed by Preston Frazier <br />A web based scoring application for Science Olympiad Tournaments. <br /><br />v1.0 (Beta) - 01.17.2016</span></label><div class="done-button"><input type="button" value="Close" id="closeAbout" class="btn btn-xs btn-primary"/></div></div>');
	
			}

            obj.focus(function () {
                $(this).next(".popBox-holder").show();
                var popBoxContainer = $(this).next().next(".popBox-container");
                var change = true;
                popBoxContainer.children('.popBox-input').css({ height: options.height, width: options.width });
                popBoxContainer.show();

                var winH = $(window).height();
                var winW = $(window).width();
                var objH = popBoxContainer.height();
                var objW = popBoxContainer.width();
                var left = (winW / 2) - (objW / 2);
                var top = (winH / 4) - (objH / 4);

                popBoxContainer.css({ position: 'fixed', margin: 0, top: (top > 0 ? top : 0) + 'px', left: (left > 0 ? left : 0) + 'px' });

				if (type == 'copyPaste') {
                	popBoxContainer.children('.popBox-input').val($(this).val().replace(RegExp(options.newlineString, "g"), "\n"));
                	popBoxContainer.children('.popBox-input').focus();
                }

                popBoxContainer.children().keydown(function (e) {
                    if (e == null) { // ie
                        keycode = event.keyCode;
                    } else { // mozilla
                        keycode = e.which;
                    }

                    if (keycode == 27) { // close
                        $(this).parent().hide();
                        $(this).parent().prev().hide();
                        change = false;
                    }
                });

				// Close Popup Box for bulk copying values
				var count =  1;
                popBoxContainer.children().blur(function () {
                    if (change) {
                        $(this).parent().hide();
                        $(this).parent().prev().hide();
                        if (type == 'copyPaste') {
                        	$(this).parent().prev().prev().val($(this).val().replace(/\n/g, options.newlineString));
							if (--count == 0) pasteText($(this).val(), $(this).parent().prev().prev().attr('id'));
						}
						count--;
                    }
                });
                
                // Close Popup Box for About Inf
                popBoxContainer.children().click(function() {
                if (change) {
    				 $(this).parent().hide();
                     $(this).parent().prev().hide();
    				}           
				});

            });

        });

    };

})(jQuery);
