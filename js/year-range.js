/**
* DO NOT EDIT THIS FILE.
* See the following change record for more information,
* https://www.drupal.org/node/2815083
* @preserve
**/

(function ($) {
  Drupal.facets = Drupal.facets || {};
  Drupal.behaviors.facetsDateRange = {
    attach: function attach(context, settings) {
      function toTimestamp(strDate) {
        var datum = Date.parse(strDate);
        return datum / 1000;
      }

      function autoSubmit() {
        var $this = $(this);
        var facetId = $this.parents(".facets-widget-year_range").find("ul").attr("data-drupal-facet-id");
        var daterange = settings.facets.daterange[facetId];
        var min = toTimestamp($("input[id=".concat(facetId, "_min]")).val()) || "";
        var max = toTimestamp($("input[id=".concat(facetId, "_max]")).val()) || "";
        window.location.href = daterange.url.replace("__year_range_min__", min).replace("__year_range_max__", max);
      }

      function refineSubmit() {
        var facetId = $(".facets-widget-year_range").find("ul").attr("data-drupal-facet-id");
        var daterange = settings.facets.daterange[facetId];
        var min = $("input[id=".concat(facetId, "_min]")).val();
        var max = $("input[id=".concat(facetId, "_max]")).val();
        console.log(daterange);

        // replace exist date range previous entered
        /*var params = parseQueryString(window.location.search);
        console.log(params);
          var out = [];
        for (var key in params) {
            if (params[key].indexOf("year") !== -1) {
                out.push(key + '=' + encodeURIComponent(myData[key]));
            }
        }
        var currenturl = out.join('&');
        console.log(currenturl);*/

        var daterangestr = daterange.url.replace(window.location.pathname, "");
        if (window.location.href.includes('?')) {
            // current url has query params ==> append with & instead
            daterangestr = daterangestr.replace("?", "&");
        }
        daterangestr = daterangestr.replace("__year_range_min__", min).replace("__year_range_max__", max);

        // redirect happens
        window.location.href = window.location.href + daterangestr;
        //window.location.href = daterange.url.replace("__year_range_min__", min).replace("__year_range_max__", max);
      }

      function resetRefine() {
          if (location.port != "") {
              window.location.href = window.location.protocol + '//' + window.location.hostname + ":" + location.port + window.location.pathname;
          } else {
              window.location.href = window.location.protocol + '//' + window.location.hostname + window.location.pathname;
          }
      }

      /*$("input.facet-year-range", context).on("change", autoSubmit);
      $("input.facet-year-range", context).on("keypress", function (e) {
        $(this).off("change blur");
        $(this).on("blur", autoSubmit);

        if (e.keyCode === 13) {
          autoSubmit();
        }
      });*/

        $('ul.item-list__year_range').addClass( "list-group list-group-horizontal" );
        
      $('input.facet-yearpicker-submit').click(function () {
          refineSubmit();
      });

      $('input.facet-yearpicker-reset').click(function () {
          resetRefine();
      });

        // https://adevelopersnotes.wordpress.com/2013/04/11/parsing-a-query-string-into-an-array-with-javascript/
        var parseQueryString = function( queryString ) {
            var params = {}, queries, temp, i, l;

            // Split into key/value pairs
            queries = queryString.split("&");

            // Convert the array of strings into an object
            for ( i = 0, l = queries.length; i < l; i++ ) {
                temp = queries[i].split('=');
                params[temp[0]] = temp[1];
            }

            return params;
        };


    }
  };
})(jQuery);