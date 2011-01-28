if (!window.tumblr) {
	// WARNING : global var ahead!
	tumblr = {
		shortnr: function(txt){
			if (txt.length > 100) {
				txt = txt.substr(0, 100);
				txt = txt.substr(0, txt.lastIndexOf(' ')) + '...';
			}
			return txt;
		},
		
		buildTumbls: function(json, id){
			var	posts = json.posts,
				ulString = '<ul>';

			while (posts.length > 0) {
				var p = posts.shift(),
					li = ['<li><a href="', p.url, '">'],
					txt;

				switch(p.type){
					case 'audio':
						txt = p['audio-caption'].replace(/<\/?[A-Za-z]+>/g, '') || '(audio)';
						break;
					case 'conversation':
						txt = p['conversation-title'];
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

				li.push(txt, '</a></li>');

				ulString += li.join('');
			}

			return ulString;
		},
		
		writeTumblrList: function(divId, json){
			var	blog = json.tumblelog,
				posts = tumblr.buildTumbls(json),
				widget	= $("#" + divId);

			widget.closest("li").children("h3")
				.addClass("tumblr-list")
				.empty().append(blog.title)
				.wrap('<a href="http://'+blog.name+'.tumblr.com/" />');
			widget.append(posts);
		}
	};
}