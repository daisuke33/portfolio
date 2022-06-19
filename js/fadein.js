'use strict';

// ============== サイトサクセス時のアニメーション
$(function() {
	setTimeout(function(){
		$('.fadeIn img').fadeIn(1000);
	},200);
	setTimeout(function(){
		$('.fadeIn').fadeOut(1000);
	},3000);
});

// ============== 各セクション ふわっと下から
function fadeInDisplay() {
  const windowHeight = $(window).height();
  const scroll = $(window).scrollTop();
  $('.under-fadein').each(function () {
    const targetPosition = $(this).offset().top;
    if (scroll > targetPosition - windowHeight + 100) {
      $(this).addClass("add-fadein");
    }
  });
}

// リロード時、スクロール前でもアクション実行されるように
fadeInDisplay();

$(window).scroll(function () {
  fadeInDisplay();
});


// =============== aboutセクション 浮き出る表示のアクション ===============
// リロード時、スクロール前でもアクション実行されるように
// aboutFadeInDisplay ();

// $(window).scroll(function () {
//   aboutFadeInDisplay ();
// });
// // 関数化
// function aboutFadeInDisplay () {
//   const windowHeight = $(window).height();
//   const scroll = $(window).scrollTop();
//   $('.js-img').each(function () {
//     const targetPosition = $(this).offset().top;
//     if (scroll > targetPosition - windowHeight + 100) {
//       $(this).addClass("is-fadein");
//     }
//   });
//   $('.js-author').each(function () {
//     const targetPosition = $(this).offset().top;
//     if (scroll > targetPosition - windowHeight + 100) {
//       $(this).addClass("is-fadein");
//     }
//   });
// }