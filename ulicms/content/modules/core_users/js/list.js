/* global bootbox */

$(() => {
	$(".avatar").click((event) => {
		const target = $(event.currentTarget);
                
		const img = document.createElement("img");
		img.src = target.attr("src");

		const headline = document.createElement("h3");
		headline.innerText = target.data("name");

		const wrap = document.createElement("div");
		wrap.append(headline);
		wrap.append(img)

		bootbox.alert(wrap.outerHTML);
	});
});