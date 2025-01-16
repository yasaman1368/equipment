jQuery(document).ready(function($) {
    let rotate = false;
    let menuSection = $('.question')
    $('#arrowIcon').on('click', function() {
      const el = $(this);
      el.css('transition', 'transform 1s ease');

      if (!rotate) {
        el.css('transform', 'rotate(1620deg)');
        menuSection.css({
          "width": '150px',
        })
        rotate = true;
      } else {
        el.css('transform', 'rotate(3600deg)');
        menuSection.css({
          "width": '51px',
        })
        rotate = false;
      }
    });

// panel  for close modal button
$('span.colse-square').click(function(e) {
  e.preventDefault();
  $(".container-modal-show-scores").hide();
});

 //  Initialize Swiper
 var swiper = new Swiper('.swiper', {
  slidesPerView: 1,
  spaceBetween: 10,
  navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
  },
  pagination: {
      el: '.swiper-pagination',
      clickable: true,
  },
  scrollbar: {
      el: '.swiper-scrollbar',
      hide: true,
  },
   breakpoints: { 640: { slidesPerView: 1 }, 768: { slidesPerView: 2 }, 1024: { slidesPerView: 3 }},
  autoplay: { delay: 2500, // Time (in ms) between slide transitions,
       disableOnInteraction: false, // Autoplay will not stop after user interactions },
  }
});
$('.reply-comment').click(function (e) { 
  e.preventDefault();
 el=$(this)
 elCommentId=$(this).data('comment-id');
 elCommentAuthor=$(this).data('comment-author');
  $('#comment_parent').val( elCommentId);
  $('#replytext').text(elCommentAuthor);
});
let commentDate=$('.date-set-comment');

for(element in commentDate){
  examDate=new Date(commentDate[element].innerHTML)
  let showFormattedDate = examDate.toLocaleDateString('fa-IR');
  commentDate[element].innerHTML=showFormattedDate
  
}



  });
   
  //js codes
  
let pageNumber = 1;
const loadMorePostsBtn = document.getElementsByClassName('load-more-posts-btn');
const ajaxElement = document.getElementById('ajax-url');
const ajaxUrl = ajaxElement ? ajaxElement.dataset.ajaxUrl  : '';
const archiveContainer = document.getElementById('archive-content');

// Use event delegation for better performance
document.addEventListener('click', (event) => {
    if (event.target.classList.contains('load-more-posts-btn')) {
        loadMorePosts();
    }
});

async function loadMorePosts() {
        try {
            const response = await fetch(ajaxUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
                body: new URLSearchParams({
                    action: 'porsnegar_pagination',
                    pageNumber: pageNumber
            })
            });

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }  
            
            const data = await response.json(); // Parse the JSON response
        
            if (data.success) {
                // Handle the success case
                const posts = data.nextPosts; 
            archiveContainer.insertAdjacentHTML('beforeend', posts); // Safely append HTML
            pageNumber = data.pageNumber;
            if (pageNumber === 'end') {

                Array.from(loadMorePostsBtn).forEach(element => {   
                    element.remove() // Correct way to hide elements
                });
                }
            } else {
                console.error('Error:', data); // Handle the error message
            }
        } catch (error) {
        console.error('Error:', error);
        }
}
