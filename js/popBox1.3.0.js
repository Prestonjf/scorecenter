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
* Modified By: Preston Frazier
*/

(function ($) {
	
	var about = 'Tournament Score Center (TSC) - Tournament scoring web application.<br />'+
	'Copyright (C) 2016  Preston Frazier<br /><br />'+

    'This program is free software: you can redistribute it and/or modify<br />'+
    'it under the terms of the GNU General Public License as published by<br />'+
    'the Free Software Foundation, either version 3 of the License, or<br />'+
    '(at your option) any later version.<br /><br />'+

    'This program is distributed in the hope that it will be useful,<br />'+
    'but WITHOUT ANY WARRANTY; without even the implied warranty of<br />'+
    'MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the<br />'+
    'GNU General Public License for more details.<br /><br />'+

    'You should have received a copy of the GNU General Public License<br />'+
    'along with this program.  If not, see <a href="http://www.gnu.org/licenses/" target="_blank">http://www.gnu.org/licenses/</a>.<br /><br />'+
    
    '@package: Tournament Score Center (TSC) - Tournament scoring web application.<br />'+
    '@version: 1.16.2, 09.05.2016<br />'+
    '@author: Preston Frazier <a href="http://scorecenter.prestonsproductions.com/index.php" target="_blank">http://scorecenter.prestonsproductions.com/index.php</a><br />'+ 
    '@license: <a href="http://www.gnu.org/licenses/gpl-3.0.en.html" target="_blank">http://www.gnu.org/licenses/gpl-3.0.en.html</a> GPLv3<br />';
	
	
	
    $.fn.popBox = function (options, type) {

        var defaults = {
            height: 100,
            width: 300,
            newlineString: "<br/>"
        };
        var options = $.extend(defaults, options);


        return this.each(function () {

            obj = $(this);

            var inputName = 'popBoxInput' + obj.attr('id');
            var labelValue = $("label[for=" + obj.attr('id') + "]").text();
			
			if (type == 'copyPaste') {
            	obj.after('<div class="popBox-holder"></div><div class="popBox-container"><label style="text-align:left;"><span style="font-weight:normal;font-size:14px;"><h4>Bulk Copy</h4>1. Primary and Alternate Teams must be copied and pasted separately. <br />2. Copy values from a spreadsheet (in corresponding team order as this screen) <br />3. Paste them in the text field below. Each row must be a number or blank. <br />4. Once finished, select done.</label><br /><label style="display: none;" for="' + inputName + '">' + labelValue + '</span></label><textarea id="' + inputName + '" name="' + inputName + '" class="popBox-input" /><div class="done-button"><input type="button" value="Done" class="btn btn-xs btn-primary" id="bulkCopyButton" /></div></div>');
			}
			else if (type == 'about') {
			
				obj.after('<div class="popBox-holder"></div><div class="popBox-container"><label style="text-align:left;"><span style="font-weight:normal;font-size:14px;">'+about+'</span></label><div class="done-button"><input type="button" value="Close" id="closeAbout" class="btn btn-xs btn-primary"/></div></div>');
	
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
                popBoxContainer.children().click(function () {
                    if (change && $('input',this).attr('id') == 'bulkCopyButton') {
                       popBoxContainer.children('.popBox-input').parent().hide();
                        popBoxContainer.children('.popBox-input').parent().prev().hide();
                        if (type == 'copyPaste') {
                        	popBoxContainer.children('.popBox-input').parent().prev().prev().val(popBoxContainer.children('.popBox-input').val().replace(/\n/g, options.newlineString));
							if (--count == 0) pasteText(popBoxContainer.children('.popBox-input').val(), popBoxContainer.children('.popBox-input').parent().prev().prev().attr('id'));
						}
						count--;
                    }
                });
                
                // Close Popup Box for About Inf
                popBoxContainer.children().click(function() {
                if (change && $('input',this).attr('id') == 'closeAbout') {
    				 $(this).parent().hide();
                     $(this).parent().prev().hide();
    				}           
				});

            });

        });

    };

})(jQuery);
