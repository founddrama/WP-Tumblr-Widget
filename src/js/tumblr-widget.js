/**
 * @namespace {tumblr}
 * @requires {jQuery} - http://jquery.com/
 */
if (!window.tumblr) {
	// WARNING : global var ahead!
	tumblr = {
		/**
		 * @param {String} txt - the text to shorten
		 * @return {String} the shortened text
		 */
		shortnr: function(txt){
			if (txt.length > 100) {
				txt = txt.substr(0, 100);
				txt = txt.substr(0, txt.lastIndexOf(' ')) + '...';
			}
			return txt;
		},
		
		/**
		 * @param {JSON} json
		 * @return {String} html-as-string for the ul
		 */
		buildTumbls: function(json){
			var	posts = json.posts,
				ul = ['<ul>'],
				p,
				txt;

			while (posts.length > 0) {
				p = posts.shift();
				
				ul.push('<li><a href="', p.url, '">');

				switch(p.type){
					case 'audio':
						txt = p['audio-caption'].replace(/<\/?[A-Za-z]+>/g, '') || '(audio)';
						break;
					case 'conversation':
						txt = p['conversation-title'] || tumblr.shortnr(p['conversation-text']);
						break;
					case 'link':
						txt = p['link-text'];
						break;
					case 'photo':
						txt = p['photo-caption'] || 'uncaptioned';
						break;
					case 'quote':
						txt = tumblr.shortnr(p['quote-text']);
						break;
					case 'regular':
						txt = tumblr.shortnr(p['regular-title'] || p['regular-body']);
						break;
					case 'video':
						txt = tumblr.shortnr(p['video-caption'] || '(video)');
						break;
				}

				ul.push(txt, '</a></li>');
			}
			
			ul.push('</ul>');
			return ul.join('');
		},
		
		/**
		 * @param {String} divId - the id of the destination div
		 * @param {JSON} json
		 */
		writeTumblrList: function(divId, json){
			var	blog = json.tumblelog,
				posts = tumblr.buildTumbls(json),
				widget = $('#' + divId);

			widget.closest('li').children('h3')
				.addClass('tumblr-list')
				.empty().append(blog.title)
				.wrap('<a href="http://'+blog.name+'.tumblr.com/" />');
			widget.append(posts);
		}
	};
}