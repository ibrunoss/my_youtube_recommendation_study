MyYoutubeRecommendationStudy.loadVideos(my_yt_rec_s_ajax.url).then((value) => {
  MyYoutubeRecommendationStudy.listCallbacks.forEach((item) => {
    item.callback(value, item.container, item.layout, item.limit, item.lang);
  });
});