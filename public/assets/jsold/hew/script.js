$(".show_sidebar").click(function (e) {
  $("#target").animate({ width: "toggle" }, 500).addClass("open");
  $("#listing_filter.open_filter").hide(0).removeClass("open_filter");
  $("#filter.open_filter").hide(0).removeClass("open_filter");
  // document.body.style.overflow = "hidden";
  e.stopPropagation();
  return false;
});
$("#target").click(function (e) {
  e.stopPropagation();
});
$(".hide_sidebar").click(function () {
  $("#target").animate({ width: "toggle" }, 500).removeClass("open");
  // document.body.style.overflow = "";
  return false;
});
$("#target .overlay").click(function () {
  $("#target").animate({ width: "toggle" }, 500).removeClass("open");
  // document.body.style.overflow = "";
  return false;
});

$(document).ready(function () {
  $("input[name=action]").change(function () {
    var test = $(this).val();
    $(".show-hide").hide();
    $("#" + test).show();
  });
});

$(document).ready(function () {
  $(".hover-trigger").hover(
    function () {
      // On mouseenter
      var targetId = $(this).data("target-id");
      $("#" + targetId).show(); // Show the related div
    },
    function () {
      // On mouseleave
      var targetId = $(this).data("target-id");
      $("#" + targetId).hide(); // Hide the related div
    }
  );

  $("set a.set-down").on("click", function () {
    if ($(this).hasClass("active-bg")) {
      $(this).removeClass("active-bg");
    } else {
      $(".set").removeClass("active-bg");
      $(this).addClass("active-bg");
    }
  });

  $(".set a.set-down").on("click", function () {
    if ($(this).hasClass("active")) {
      $(this).removeClass("active");
      $(this).siblings(".acc-content").slideUp(200);
      $(".set a.set-down i")
        .removeClass("fa-angle-up")
        .addClass("fa-angle-down");
    } else {
      $(".set a.set-down i")
        .removeClass("fa-angle-up")
        .addClass("fa-angle-down");
      $(this).find("i").removeClass("fa-angle-down").addClass("fa-angle-up");
      $(".set > a.set-down").removeClass("active");
      $(this).addClass("active");
      $(".acc-content").slideUp(200);
      $(this).siblings(".acc-content").slideDown(200);
    }
  });
});

document.addEventListener("DOMContentLoaded", function () {
  const tableRows = document.querySelectorAll("tr[data-target-div]");
  tableRows.forEach((row) => {
    row.addEventListener("mouseenter", function () {
      const targetDivId = this.dataset.targetDiv;
      const targetDiv = document.getElementById(targetDivId);
      if (targetDiv) {
        targetDiv.style.display = "block"; // Or 'flex', 'grid', etc.
      }
    });

    row.addEventListener("mouseleave", function () {
      const targetDivId = this.dataset.targetDiv;
      const targetDiv = document.getElementById(targetDivId);
      if (targetDiv) {
        targetDiv.style.display = "none";
      }
    });
  });
});

//sidebar
