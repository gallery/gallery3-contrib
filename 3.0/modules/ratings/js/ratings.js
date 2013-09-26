/**
 * RabidRatings - Simple and Pretty Ratings for Everyone
 * JavaScript functionality requires MooTools version 1.2 <http://www.mootools.net>.
 * 
 * Full package available at <http://widgets.rabidlabs.net/ratings>.
 *
 * NOTE: The included JavaScript WILL ONLY WORK WITH MOOTOOLS.  It will not work if any other JavaScript
 * framework is present on the page.
 *
 * Current MooTools version: 1.2
 *
 * @author Michelle Steigerwalt <http://www.msteigerwalt.com>
 * @copyright 2007, 2008 Michelle Steigerwalt
 * @license LGPL 2.1 <http://creativecommons.org/licenses/LGPL/2.1/>
 */

var RabidRatings = new Class({

	Implements: Options,
	
	options: {
		url: null,
		leftMargin: 0,  /* The width in pixels of the margin before the stars. */
		starWidth: 17,  /* The width in pixels of each star. */
		starMargin: 4,  /* The width in pixels between each star. */
		scale: 5,       /* It's a five-star scale. */
		snap: 1         /* Will snap to the nearest star (can be made a decimal, too). */
	},
	
	initialize: function(options) {
		
		this.setOptions(options);
		var activeColor = this.options.activeColor;
		var votedColor  = this.options.votedColor;
		var fillColor   = this.options.fillColor;

		$$('.rabidRating').each(function(el) {
		//Does this if the browser is NOT IE6. IE6 users don't deserve fancy ratings. >:(
		if (!Browser.Engine.trident4) {
			el.id = el.getAttribute('id');
			el.wrapper = el.getElement('.wrapper');
			el.textEl = el.getElement('.ratingText');
			el.offset = el.getPosition().x;
			el.fill = el.getElement('.ratingFill');
			el.starPercent = this.getStarPercent(el.id);
			el.ratableId   = this.getRatableId(el.id);
			this.fillVote(el.starPercent, el);
			el.currentFill = this.getFillPercent(el.starPercent);
			el.morphFx = new Fx.Morph(el.fill, {'link':'chain'});
			el.widthFx = new Fx.Tween(el.fill, {property:'width', link: 'chain'});

			el.mouseCrap = function(e) { 
				var fill = e.client.x - el.offset;
				var fillPercent = this.getVotePercent(fill);
				var step;
				if (this.options.snap === 0) step = 1;
				else step = (100 / this.options.scale) * this.options.snap;
				var nextStep = Math.floor(fillPercent / step) + 1;
				this.fillVote(nextStep * step, el);
			}.bind(this);

			el.wrapper.addEvent('mouseenter', function(e) { 

<? if(module::get_var("ratings","regonly") == 1 && identity::active_user()->guest){ ?>
				el.addClass('ratingVoted');
<? } else { ?>

				el.morphFx.start('.rabidRating .ratingActive');
				el.wrapper.addEvent('mousemove', el.mouseCrap);
<? } ?>
			});

			el.wrapper.addEvent('mouseleave', function(e) {
				el.removeEvent(el.mouseCrap);
				el.morphFx.start('.rabidRating .ratingFill');
				el.widthFx.start(el.currentFill);
			});

			el.wrapper.addEvent('click', function(e) {
				el.currentFill = el.newFill;
				el.morphFx.start('.rabidRating .ratingVoted');
				el.wrapper.removeEvents();
				el.addClass('ratingVoted');
				el.textEl.addClass('loading');
				var votePercent = this.getVotePercent(el.newFill);
				if (this.options.url != null) {
					var req = new Request({url:this.options.url,onComplete:el.updateText})
					.post({vote:votePercent,id:el.ratableId});	
				}
			}.bind(this));

			el.updateText = function(text) {
				error = text.split('ERROR:')[1];
				el.textEl.removeClass('loading');
				if (error) { el.showError(error); return false; }
				el.textEl.set('text', text);
			};

			el.showError = function(error) {
				el.textEl.addClass('ratingError');
				oldTxt = el.textEl.get('text');
				el.textEl.set('text', error);
				(function() {
					el.textEl.set('text', oldTxt);
					el.textEl.removeClass('ratingError');
				}).delay(1000);
			};
		} else {
			//Replaces all the fancy with a text description of the votes for IE6.
			//If you want IE6 users to have something fancier to look at, add it here.
			var plain = el.getElement('.ratingText').inject(el, 'before');
			el.remove();
		}
		}.bind(this));
	},

	fillVote: function(percent, el) {
		el.newFill = this.getFillPercent(percent);
		if (this.getVotePercent(el.newFill) > 100) { el.newFill = this.getFillPercent(100); }
		el.fill.setStyle('width', el.newFill);
	},

	getStarPercent: function(id) {
		/* Format = anyStringHere-<id>-<float(currentStars)>_(scale); 
		 * Example: rabidRatings-5-3_5 //Primary key id = 5, 3/5 stars. */
		var stars = id.match(/(\d*)-(\d*\.?\d+)_(\d*\.?\d+)$/);
		var ratableId = stars[1].toFloat();
		var score = stars[2].toFloat();
		var scale = stars[3].toFloat();
		var percent =  (score / scale) * 100;
		return percent;
	},

	getFillPercent: function (starPercent) {
		return (starPercent/100)*((this.options.starWidth+this.options.starMargin)*this.options.scale) + this.options.leftMargin;
	},

	getVotePercent: function(divPosition) {
		var starsWidth = (this.options.starWidth+this.options.starMargin)*this.options.scale;
		var offset = this.options.leftMargin;
		var starPosition = divPosition - this.options.leftMargin;
		var percent = (starPosition / starsWidth * 100).round(2);
		return percent;
	},

	getRatableId: function(id) {
		var stars = id.match(/(\d*)-(\d*\.?\d+)_(\d*\.?\d+)$/);
		return stars[1];
	}

});

window.addEvent('domready', function(e) {
	<?
		$regonly = module::get_var("ratings","regonly");
		$votestring = module::get_var("ratings","votestring");
		$imageword = module::get_var("ratings","imageword");
		$ratingurl = url::file("modules/ratings/vendor/ratings.php");
		$ratingurl .= "?modpath=".MODPATH;
		$ratingurl .= "&varpath=".VARPATH;
		$ratingurl .= "&votestring=".$votestring;
		$ratingurl .= "&imageword=".$imageword;
		$ratingurl .= "&userid=".identity::active_user()->id;
		if(identity::active_user()->guest && $regonly == 1){
		  $ratingurl .= "&regonly=".$regonly;
		}
		$ratingurl = preg_replace("/\/index\.php/","",$ratingurl);
	?>
	var ratingurl = "<?= $ratingurl; ?>";
	var rating = new RabidRatings({
		url:ratingurl,
		snap: .5         /* Will snap to the nearest star (can be made a decimal, too). */
	});
});
