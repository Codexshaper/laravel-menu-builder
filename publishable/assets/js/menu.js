(function($){
	$(".menu_item").each(function(e){
		var self = $(this);
		self.removeClass("active");
		self.find(">a").on("click", function(e){
			var onClickChild = self.find("> .onClick");

			if(onClickChild.hasClass("onClick")) {
				e.preventDefault();
				self.toggleClass("active");
				onClickChild.slideToggle("slow");
			}

		});

		var onClickChild = self.find("> .onClick");
		var onHoverChild = self.find("> .onHover");

		if(onClickChild.hasClass("onClick")) {
			self.toggleClass("hasClickChild");
		}

		if(onHoverChild.hasClass("onHover")) {
			self.toggleClass("hasHoverChild");
		}
	});

	$(".onHover").each(function(){
		var child = $(this);
		var parent = child.parent();
		parent.removeClass("active");
		parent.hover(function(){
			parent.toggleClass("active");
			child.toggleClass("active");
		});
	});

	$(".show-sidebar").on("click", function(){
		$(".sidebar").toggleClass("sm-device");
	});

	$(document).on("click", "#show_menu_design", function(e){
		var id = $(this).data('id');
		var prefix = $(this).data('prefix');
		var action = prefix+'/menu/html/'+id;

		$.ajax({
			url: action,
			method: 'GET',
			responseType: 'json',
			success: function(data){
				$(".menu_items").html(data.html);

				$(".menu_items .menu_item").each(function(e){
					var self = $(this);
					self.removeClass("active");
					self.find(">a").on("click", function(e){
						var onClickChild = self.find("> .onClick");

						if(onClickChild.hasClass("onClick")) {
							e.preventDefault();
							self.toggleClass("active");
							onClickChild.slideToggle("slow");
						}

					});

					var onClickChild = self.find("> .onClick");
					var onHoverChild = self.find("> .onHover");

					if(onClickChild.hasClass("onClick")) {
						self.toggleClass("hasClickChild");
					}

					if(onHoverChild.hasClass("onHover")) {
						self.toggleClass("hasHoverChild");
					}
				});

				$(".menu_items .onHover").each(function(){
					var child = $(this);
					var parent = child.parent();
					parent.removeClass("active");
					parent.hover(function(){
						parent.toggleClass("active");
						child.toggleClass("active");
					});
				});
			},
			error: function(err){
				console.log(err);
			}

		});
	});
})(jQuery);