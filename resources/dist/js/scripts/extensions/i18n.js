/*=========================================================================================
    File Name: i18n.js
    Description: Internationalization
    --------------------------------------------------------------------------------------
    Item name: Vuexy  - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: PIXINVENT
    Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/

// For Language Select
var changeText = $('.card-localization .card-text')

// for extensions i18n page
// i18n has been initialized already we have have to add resource bundle
i18next.addResourceBundle('en_p', 'translation', {
  key: "Cake sesame snaps cupcake gingerbread danish I love gingerbread. Apple pie pie jujubes chupa chups muffin halvah lollipop. Chocolate cake oat cake tiramisu marzipan sugar plum. Donut sweet pie oat cake dragée fruitcake cotton candy lemon drops."
});

i18next.addResourceBundle('pt_p', 'translation', {
  key: "O sésamo do bolo agarra dinamarquês do pão-de-espécie do queque eu amo o pão-de-espécie. Torta de torta de maçã jujubes chupa chups  pirulito halvah muffin. Ameixa do açúcar do maçapão do tiramisu do bolo da aveia do bolo de chocolate. Donut doce aveia torta  dragée fruitcake algodão doce gotas de limão."
});

i18next.addResourceBundle('fr_p', 'translation', {
  key: "Gâteau au sésame s'enclenche petit pain au pain d'épices danois J'adore le pain d'épices. Tarte aux pommes jujubes chupa chups  muffin halva sucette. Gateau au chocolat gateau d \ 'avoine tiramisu prune sucre. Donut tourte sucrée gateau dragée fruit gateau barbe a papa citron gouttes.."
});
i18next.addResourceBundle('de_p', 'translation', {
  key: "Kuchen Sesam Snaps Cupcake Lebkuchen dänisch Ich liebe Lebkuchen. Apfelkuchen Jujubes Chupa Chups Muffin Halwa Lutscher. Schokoladenkuchen-Haferkuchen-Tiramisumarzipanzuckerpflaume. Donut süße Torte Hafer Kuchen Dragée Obstkuchen Zuckerwatte Zitronentropfen."
});

// Change Card Text Language On Click
$(".language-options").on("click", ".i18n-lang-option", function () {
  var selected_lng = $(this).data('lng');
  i18next.changeLanguage(selected_lng, function (err, t) {
    // resources have been loaded
    changeText.localize();
  });
})
