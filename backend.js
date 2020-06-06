'use strict';
console.log('script loaded');

//jQuery(function () {
	var example1 = new Vue({
			el: '#v',
			data: {
				filters: filters,
				plugins: plugins
			},
			methods: {
				addQuestion: addQuestion,
				deleteQuestion: deleteQuestion,
				moveQuestion: moveQuestion,
				expandTextarea:expandTextarea,
			},
			computed: {
				JSONdata: function () {
					//console.log(JSON.stringify(this.filters));
					return JSON.stringify(this.filters);
				}
			}
		})
//});

//});

function addQuestion(index) {
	filters.splice(index, 0, {plugins:[],query_string:''})
}
function deleteQuestion(index) {
	filters.splice(index, 1)
}
function moveQuestion(index) {
	var newIndex = prompt('New position', index + 1);
	if (!newIndex || isNaN (newIndex) || newIndex == index + 1)	return;
	newIndex -= 1;
	if (newIndex<0) newIndex = 0;
	if (newIndex>=filters.length) newIndex = filters.length -1;

	filters.splice(newIndex, 0, filters.splice(index, 1)[0]);
}

function expandTextarea(event) {
	 console.log([event.target]);
	//var $el = jQuery('textarea');
//	console.log($el);
//		$el.on('keyup', function () {
			//event.target.style.overflow = 'hidden';
			event.target.style.minHeight = 0;
			event.target.style.minHeight = event.target.scrollHeight + (event.target.offsetHeight - event.target.clientHeight) + 'px';
			event.target.style.minHeight = event.target.scrollHeight + (event.target.offsetHeight - event.target.clientHeight) + 'px';
			
			//event.target.style.height = event.target.offsetHeight + 'px';
			console.log('a');
			console.log(event.target.offsetHeight - event.target.clientHeight);
		//}, false);
/*	if (!$el.val()) {
		el.rows = 2
	} else*/
		//$el.dispatchEvent(new Event('keyup'));
	//el.classList.remove('small', 'medium');
}
