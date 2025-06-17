<template>
  <div>
    <div v-if="sliderimage.length > 0">
    <v-carousel :show-arrows="true" hide-delimiter-background delimiter-icon="mdi-minus" height="200" cycle class="mobile">
      <v-carousel-item v-for="(image, i) in sliderimage" :key="i" :src=url+(image.image_url) class="bcenter"></v-carousel-item>
    </v-carousel>
    </div>
    <div v-else>
      <v-carousel :show-arrows="true" hide-delimiter-background delimiter-icon="mdi-minus" height="200" cycle class="mobile">
        <v-carousel-item v-for="(image, i) in dummyImages" :key="i" :src=image.src></v-carousel-item>
      </v-carousel>
    </div>
    <v-container class="pa-0" style="max-width: 1300px;">
  <div v-if="sliderimage.length > 0">
    <v-carousel
      :show-arrows="true"
      hide-delimiter-background
      delimiter-icon="mdi-minus"
      height="300"
      cycle
      class="desktop"
    >
      <v-carousel-item
        v-for="(image, i) in sliderimage"
        :key="i"
        :src="url + image.image_url"
        cover
      ></v-carousel-item>
    </v-carousel>
  </div>
  <div v-else>
    <v-carousel
      :show-arrows="true"
      hide-delimiter-background
      delimiter-icon="mdi-minus"
      height="300"
      cycle
      class="desktop"
    >
      <v-carousel-item
        v-for="(image, i) in dummyImages"
        :key="i"
        :src="image.src"
        cover
      ></v-carousel-item>
    </v-carousel>
  </div>
</v-container>

    <v-container>

      <v-row class="justify-content-md-start desktop">
        <v-col v-if="bprofile.user_avatar == null" cols="12" sm="3" md="2">
          <v-img cover class="rounded-circle" width="150" :src="('https://dummyimage.com/150x150/1976d2/ffffff.jpg&text= '+bprofile.first_last_letter+' ')"></v-img>
        </v-col>
        <v-col v-else cols="12" sm="3" md="2">
          <v-img cover class="rounded-circle" width="150" :src=(url+bprofile.user_avatar) ></v-img>
        </v-col>
        <v-col cols="12" sm="7" md="8">
          <h2 class="bold mb-0">{{bprofile.name}}</h2>
            <div v-if="loggedUser == false">
                  <span class="me-1" @click="toggleLike">
                   Add to Wishlist<v-icon>{{ 'mdi-heart-outline' }}</v-icon>
                  </span>
            </div>
            <div v-else>
                  <span v-if="bprofile.user_business_wishlist!=null">
                    <span class="me-1 wishlist-icon" @click="toggleLike">
                      Add to Wishlist<v-icon :color="heartColor">
                        {{ isLiked ? 'mdi-heart' : 'mdi-heart-outline' }}
                      </v-icon>
                    </span>
                  </span>
                <span v-else>
                    <span class="me-1 wishlist-icon" @click="toggleLike">
                      Add to Wishlist<v-icon :color="heartColor">{{ isLiked ? 'mdi-heart' : 'mdi-heart-outline' }}</v-icon>
                    </span>
                  </span>
            </div>
          <div class="ratings align-center d-flex">
            <v-rating :value="(bprofile.allreview_avg_rating)" color="amber" dense half-increments size="20" readonly></v-rating>
            <div v-if="bprofile.allreview_avg_rating === null">
              <p class="rating mb-2"> No Reviews
                <span class="write-a-review">
                 <a href="#accordionReview" v-b-toggle.accordion-7>View all</a>
              </span>
              </p>
            </div>
            <div v-else>
              <div class="rating"> <span class="badge bg-dark ms-1">{{ bprofile.allreview_avg_rating }}</span>
                <span class="write-a-review ms-1">
                 <a href="#accordionReview" class="mt-16" v-b-toggle.accordion-7>View all</a>
              </span>
              </div>
            </div>
          </div>
          <div class="mb-2">
            <div class="write-a-review">
                <span v-if=" loggedUser == false ">
                  <a href="/user/login">Write a Review</a>
                </span>
                <span v-else>
                  <a v-if="bprofile.user_added_reviewed==null" @click="openReviewModal">Write a Review</a>
                  <a v-else>Reviewed</a>
                </span>
            </div>
          </div>
          <div class="d-flex">
            <a :href="'tel:+' + bprofile.phonecode + bprofile.mobile_phone">
              <v-btn color="bgred" class="me-1 mb-1 text-light small-text">
                <v-icon left> mdi-phone </v-icon>
                Call Now
              </v-btn>
            </a>
            <a :href="('https://wa.me/'+'+'+bprofile.phonecode+bprofile.mobile_phone+'?text=Enquiry from Bizlx : '+currentURL)" target="_blank">
              <v-btn color="success" class="me-1 mb-1 small-text">
                <v-icon left> mdi-whatsapp </v-icon>
                Whatsapp
              </v-btn>
            </a>
            <a href="#accordionContact" v-b-toggle.accordion-6>
              <v-btn color="success" class="me-1 mb-1 small-text">
                <v-icon left> mdi-map-marker </v-icon>
                Location
              </v-btn>
            </a>
            <v-speed-dial  v-model="fab" :direction="direction" :open-on-hover="hover" :transition="transition">
              <template v-slot:activator>
                <v-btn v-model="hover" color="blue darken-2" dark>
                  Share
                  <v-icon> mdi-share-variant </v-icon>
                </v-btn>
              </template>
              <v-btn fab dark small color="blue" @click="shareOnFacebook">
                <v-icon>mdi-facebook</v-icon>
              </v-btn>
              <v-btn fab dark small color="green" @click="shareOnWhatsApp">
                <v-icon>mdi-whatsapp</v-icon>
              </v-btn>
            </v-speed-dial>
          </div>
        </v-col>
        <v-col cols="12" sm="2" md="2">
          <v-row>
            <v-col>
              <v-snackbar v-model="snackbar" :timeout="timeout">{{ text }}
                <template v-slot:action="{}">
                  <v-btn color="blue" text @click="snackbar = false">Close</v-btn>
                </template>
              </v-snackbar>
            </v-col>
          </v-row>
        </v-col>
      </v-row>

      <v-row class="justify-content-center mobile text-center">
          <v-col v-if="bprofile.user_avatar == null" cols="12" sm="3" md="2" class="pb-0">
            <v-img cover class="mx-auto rounded-circle" width="75" :src="('https://dummyimage.com/150x150/1976d2/ffffff.jpg&text= '+bprofile.first_last_letter+'')"></v-img>
          </v-col>
          <v-col v-else cols="12" sm="3" md="2" class="pb-0">
            <v-img cover class="mx-auto rounded-circle" width="75" :src=(url+bprofile.user_avatar)></v-img>
          </v-col>
          <v-col cols="12" sm="7" md="8" class="py-1">
            <h5 class="fw-bolder cblu">{{bprofile.name}}</h5>
            <div class="ratings align-center d-flex justify-center my-1">
              <v-rating :value="(bprofile.allreview_avg_rating)" color="amber" dense half-increments size="14" readonly></v-rating>
              <div v-if="bprofile.allreview_avg_rating == null"><span class="rating small">No Reviews</span></div>
              <div v-else>
                <span class="rating small">
                  <span class="badge bg-dark ms-1">{{ bprofile.allreview_avg_rating }}</span>
                  <span class="write-a-review ms-2">
                  <a href="#accordionReview" v-b-toggle.accordion-7 class="mreview" style="font-size: 14px">View all</a>
              </span>
                </span>
              </div>
            </div>
            <div v-if="bprofile.allreview_avg_rating == null">
              <p class="rating mb-1 small"> Rating : No Reviews
                <span class="write-a-review">
                   <a href="#accordionReview" v-b-toggle.accordion-7 class="mreview">View all</a>
                </span>
              </p>
            </div>
            <div v-else>
              <span class="mreview">
                <a v-if=" loggedUser == false "><a href="/user/login">Write a Review</a></a>
                <a v-else @click="openReviewModal" >Write a Review</a>
              </span>
            </div>
          </v-col>
          <v-col cols="12" class="d-flex justify-center py-1 sc1">
            <a :href="'tel:+' + bprofile.phonecode + bprofile.mobile_phone">
              <v-btn color="indigo" small outlined fab>
                <v-icon small> mdi-phone </v-icon>
              </v-btn>
              <div class="small text-center">Call</div>
            </a>
            <a href="#accordionContact" v-b-toggle.accordion-6 class="mx-2">
              <v-btn color="grey" small outlined fab>
                <v-icon small color="primary"> mdi-map-marker </v-icon>
              </v-btn>
              <div class="small text-center">Location</div>
            </a>
            <a :href="('https://wa.me/'+'+'+bprofile.phonecode+bprofile.mobile_phone+'?text=Enquiry from Bizlx : '+currentURL)" target="_blank" >
              <v-btn color="success" small outlined fab >
                <v-icon small> mdi-whatsapp </v-icon>
              </v-btn>
              <div class="small text-center">Whatsapp</div>
            </a>
          </v-col>
          <v-col cols="6" class="d-none d-flex pt-0">
                <div style="max-width: 120px; width: 120px;">
                      <span class="mt-1 small" style="color: red;">
                      Share
                    </span>
                  <v-btn fab dark small color="blue" @click="shareOnFacebook">
                    <v-icon>mdi-facebook</v-icon>
                  </v-btn>
                  
                  <v-btn fab dark small color="green" @click="shareOnWhatsApp">
                    <v-icon>mdi-whatsapp</v-icon>
                  </v-btn>
                </div>
          </v-col>
          <v-col cols="6 d-flex justify-end pt-0">
          <div v-if="loggedUser == false">
                  <span class="me-1" @click="toggleLike">
                   Add to Wishlist<v-icon>{{ 'mdi-heart-outline' }}</v-icon>
                  </span>
            </div>
            <div v-else>
                  <span v-if="bprofile.user_business_wishlist!=null">
                    <span class="me-1 wishlist-icon" @click="toggleLike">
                      Add to Wishlist<v-icon :color="heartColor">
                        {{ isLiked ? 'mdi-heart' : 'mdi-heart-outline' }}
                      </v-icon>
                    </span>
                  </span>
                <span v-else>
                    <span class="me-1 wishlist-icon" @click="toggleLike">
                      Add to Wishlist<v-icon :color="heartColor">{{ isLiked ? 'mdi-heart' : 'mdi-heart-outline' }}</v-icon>
                    </span>
                  </span>
            </div>
          </v-col>
      </v-row>

      <v-row>
      <v-col cols="12" sm="12" md="12" lg="4" class="side1 px-0 px-md-3">
        <div class="mobile">
          <div class="ms-2"><span class="bbred cblu fw-bold">Hot Deals</span></div>
          <div v-if="allhotdeal.length > 0">
            <carousel v-if="allhotdeal.length > 0" :autoplay="false" :margin="10" :dots="false"
                      :navText="['‹', '›']" :slideBy="1"
                      :responsive = "{ 0: { items: 1 }, 576: { items: 2 }, 768: { items: 2 }, 1200: { items: 2 } }"
                      class="vsearch mobile">
              <v-card v-for="(deal, i) in allhotdeal" :key="i" class="item mitem my-2">
                <a class="small d-flex link-dark" :href="'/deals/detail/'+deal.hotdeal_slug">
                  <div class="mimg">
                    <a :href="'/deals/detail/'+deal.hotdeal_slug" class="link-dark text-decoration-none">
                      <div v-if="deal.hotdeals_images.length == 0 ">
                        <v-img height="110" cover :src="('https://dummyimage.com/100x100/#bbbfc2/fff&text=No image')" ></v-img>
                      </div>
                      <div v-else>
                        <v-img height="110" cover :src=url+deal.hotdeals_images[0].hotdeal_img_url class="rounded"> </v-img>
                      </div>
                    </a>
                  </div>
                  <div class="mdetails">
                    <a :href="'/deals/detail/'+deal.hotdeal_slug" class="mtitle link-dark text-decoration-none">
                      {{ deal.hot_deal_title }}
                    </a>
                    <p class="mb-0 fs11 cred">Price:
                      <span style="color: black;">₹{{ deal.price_to }}</span>
                    </p>
                    <p class="mb-0 fs11 cred">Validity:
                      <span style="color: black;">{{ deal.date_to }}</span>
                    </p>
                    <div class="fs11 address">
                      <v-icon small>mdi-map-marker</v-icon>
                      {{ deal.city }}, {{ deal.state }}
                    </div>
                    <div>
                      <a :href="'/deals/detail/'+deal.hotdeal_slug" class="cred fs11">
                        Read More <span>{{deal.mobile_phone}}</span>
                      </a>
                    </div>
                  </div>
                  <div class="mcontact">
                    <a :href="('https://wa.me/'+bprofile.phonecode+bprofile.mobile_phone+'?text=Enquiry from Bizlx : '+currentURL)">
                      <v-btn fab x-small color="success">
                        <v-icon>mdi-whatsapp</v-icon>
                      </v-btn>
                    </a>
                    <a :href="'tel:+' + bprofile.phonecode + bprofile.mobile_phone">
                      <v-btn fab x-small color="primary" class="my-2">
                        <v-icon>mdi-phone</v-icon>
                      </v-btn>
                    </a>
                    <a :href="'/deals/detail/'+deal.hotdeal_slug">
                      <v-btn fab x-small color="info">
                        <v-icon>mdi-eye-outline</v-icon>
                      </v-btn>
                    </a>
                  </div>
                </a>
              </v-card>
            </carousel>
          </div>
          <div v-else class="row nosales">
            <h6 class="col-12">
              <span class="ms-2">No Hot Deals</span>
            </h6>
          </div>
          <div class="ms-2"><span class="bbred cblu fw-bold">Jobs</span></div>
          <div v-if="bprofile.jobs.length > 0">
            <carousel v-if="bprofile.jobs.length > 0"  :autoplay="false" :margin="10" :dots="false"
                    :navText="['‹', '›']" :slideBy="1"
                    :responsive = "{ 0: { items: 1 }, 576: { items: 2 }, 768: { items: 3 }, 1200: { items: 2 } }"
                    class="vsearch mobile">
            <div v-for="(job, i) in bprofile.jobs" :key="i" class="item mitem my-2">
              <a :href="'/jobs/detail/'+job.job_slug" class="small d-flex link-dark text-decoration-none">
                <div class="mimg">
                  <div v-if="sliderimage.length > 0">
                    <!-- <v-img cover height="110" :src=(picm+job.business_id) ></v-img> -->
                    <v-img cover width="200" v-if="sliderimage.find(image => image.image_type === 1)"
                      :src="url + sliderimage.find(image => image.image_type === 1).image_url"  height="80"></v-img>
                  </div>
                  <div v-else>
                    <v-img cover width="200" v-for="(image, i) in dummyImages" :key="i" :src=image.src height="80"></v-img>
                  </div>
                </div>
                <div class="mdetails">
                  <a :href="'/jobs/detail/'+job.job_slug" class="mtitle link-dark text-decoration-none">
                    {{ job.job_title }}
                  </a>
                  <div class="fs11">
                  </div>
                  <div v-if="job.min_exp > 0">
                    <div class="fs11">Experience: {{ job.min_exp }} Years</div>
                  </div>
                  <div v-else>
                    <div class="fs11">Experience: Fresher</div>
                  </div>
                  <span class="fs11 fw-bold">₹{{ job.salary_from }} a Month</span>
                  <div class="fs11">
                    <v-icon small>mdi-map-marker</v-icon>{{ job.city }}, {{ job.state }}
                  </div>
                  <div>
                    <a :href="'/jobs/detail/'+job.job_slug" class="cred fs12">Read More</a>
                  </div>
                </div>
                <div class="mcontact">
                    <a :href="('https://wa.me/'+bprofile.phonecode+bprofile.mobile_phone+'?text=Enquiry from Bizlx : '+currentURL)">
                    <v-btn fab x-small color="success">
                      <v-icon>mdi-whatsapp</v-icon>
                    </v-btn>
                  </a>
                    <a :href="'tel:+' + bprofile.phonecode + bprofile.mobile_phone">
                    <v-btn fab x-small color="primary" class="my-2">
                      <v-icon>mdi-phone</v-icon>
                    </v-btn>
                  </a>
                  <a :href="'/jobs/detail/'+job.job_slug">
                    <v-btn fab x-small color="info">
                      <v-icon>mdi-eye-outline</v-icon>
                    </v-btn>
                  </a>
                </div>
              </a>
            </div>
          </carousel>
          </div>
          <div v-else class="row nosales">
            <h6 class="col-12">
              <span class="ms-2">No Jobs</span>
            </h6>
          </div>
          <div class="ms-2"><span class="bbred cblu fw-bold">Sales</span></div>
          <div v-if="bprofile.allsale.length > 0">
            <carousel v-if="bprofile.allsale.length > 0"  :autoplay="false" :margin="10" :dots="false"
                    :navText="['‹', '›']" :slideBy="1"
                    :responsive = "{ 0: { items: 1 }, 576: { items: 2 }, 768: { items: 3 }, 1200: { items: 2 } }"
                    class="vsearch mobile">
            <div v-for="(sale, i) in bprofile.allsale" :key="i" class="item mitem my-2">
              <a :href="'/sales/detail/'+sale.sale_slug" class="small d-flex link-dark text-decoration-none">
                <div class="mimg">
                  <div v-if="sale.sale_imageurl == null">
                    <v-img cover height="110" :src="('https://dummyimage.com/299x150/969096/3f4042.jpg&text='+sale.first_last_letter)"></v-img>
                  </div>
                  <div v-else>
                    <v-img cover height="110" :src=(url+sale.sale_imageurl) ></v-img>
                  </div>
                </div>
                <div class="mdetails">
                  <a :href="'/sales/detail/'+sale.sale_slug" class="mtitle link-dark text-decoration-none">
                    {{ sale.sale_title }}
                  </a>
                  <div class="fs11">
                  </div>
                  <div class="cred">Sale
                    <del class="ms-1 h6">₹{{ sale.normal_price }}</del>
                    <span class="ms-1 h5 cred">₹{{ sale.sale_price }}</span>
                  </div>
                  <div class="fs11 cred">Validity: <span style="color: black;">{{ sale.saledate_to }} </span></div>
                  <div class="fs11">
                    <v-icon small>mdi-map-marker</v-icon>
                    {{ sale.city }}, {{ sale.state }}
                    <div>
                      <a :href="'/sales/detail/'+sale.sale_slug" class="cred fs11">Read More</a>
                    </div>
                  </div>
                </div>
                <div class="mcontact">
                    <a :href="('https://wa.me/'+bprofile.phonecode+bprofile.mobile_phone+'?text=Enquiry from Bizlx : '+currentURL)">
                    <v-btn fab x-small color="success">
                      <v-icon>mdi-whatsapp</v-icon>
                    </v-btn>
                  </a>
                  <a :href="'tel:+' + bprofile.phonecode + bprofile.mobile_phone">
                    <v-btn fab x-small color="primary" class="my-2">
                      <v-icon>mdi-phone</v-icon>
                    </v-btn>
                  </a>
                  <a :href="'/sales/detail/'+sale.sale_slug">
                    <v-btn fab x-small color="info">
                      <v-icon>mdi-eye-outline</v-icon>
                    </v-btn>
                  </a>
                </div>
              </a>
            </div>
          </carousel>
          </div>
          <div v-else class="row nosales">
            <h6 class="col-12">
              <span class="ms-2">No Sales</span>
            </h6>
          </div>
        </div>
        <div class="desktop">
          <div class="accordion" role="tablist">
            <b-card no-body class="mb-1">
              <b-card-header header-tag="header" class="p-1" role="tab">
                <b-button class="w-100 catsy fw-bold d-flex justify-content-between btn-sm"
                          v-b-toggle.accordion-8 variant="light">Hot Deals</b-button>
              </b-card-header>
              <b-collapse id="accordion-8"  accordion="my-accordion-1" role="tabpanel" active>
                <b-card-body>
                  <b-card-text>
                    <div v-if="allhotdeal.length > 0">
                      <carousel v-if="allhotdeal.length > 0" :autoplay="false" :margin="30" :dots="false"
                                :navText="['‹', '›']" :slideBy="1"
                                :responsive = "{ 0: { items: 1 }, 576: { items: 2 }, 768: { items: 2 }, 1200: { items: 2 }
                         }" class="vsearch desktop">
                        <div v-for="(group, i) in allhotdeal" :key="i">
                            <a class="small link-dark" :href="'/deals/detail/'+group.hotdeal_slug">
                              <a :href="'/deals/detail/'+group.hotdeal_slug" class="link-dark text-decoration-none">
                                <div v-if="group.hotdeals_images && group.hotdeals_images.length > 0">
                                  <v-img height="80px" cover :src="url + group.hotdeals_images[0].hotdeal_img_url"></v-img>
                                </div>
                                <div v-else>
                                  <v-img height="80px" cover src="https://dummyimage.com/100x80/#bbbfc2/fff&text=No image"></v-img>
                                </div>
                              </a>
                              <div class="h160 mt-2">
                                <p class="mb-1 fs12 fw-bold">
                                  <a :href="'/deals/detail/'+group.hotdeal_slug" class="link-dark text-decoration-none">
                                    {{ group.hot_deal_title }}
                                  </a>
                                </p>
                                <div class="fs12 fw-bold">
                                </div>
                                <p class="mb-0 fs11 cred">Price:
                                  <span style="color: black;">
                                       ₹{{ group.price_to }}
                                     </span>
                                </p>
                                <p class="mb-0 fs11 cred">Validity:
                                  <span style="color: black;">{{ group.date_to }}</span></p>
                                <div class="fs12">
                                  <v-icon small>mdi-map-marker</v-icon>
                                  {{ group.city }}, {{ group.state }}
                                </div>
                                <div>
                                  <a :href="'/deals/detail/'+group.hotdeal_slug"
                                               class="cred text-decoration-none fs12">
                                    Read More
                                  </a>
                                </div>
                              </div>
                            </a>
                        </div>
                      </carousel>
                     </div>
                    <div v-else class="text-center">
                      <h5>No Hot Deals</h5>
                    </div>
                  </b-card-text>
                </b-card-body>
              </b-collapse>
            </b-card>
          </div>
          <div class="accordion" role="tablist">
            <b-card no-body class="mb-1">
              <b-card-header header-tag="header" class="p-1" role="tab">
                <b-button class="w-100 catsy fw-bold d-flex justify-content-between btn-sm"
                          v-b-toggle.accordion-9 variant="light">Jobs</b-button>
              </b-card-header>
              <b-collapse id="accordion-9" visible accordion="my-accordion-1" role="tabpanel">
                <b-card-body>
                  <b-card-text>
                    <div v-if="bprofile.jobs.length > 0">
                      <carousel v-if="bprofile.jobs.length > 0" :autoplay="false" :margin="30" :dots="false"
                                :navText="['‹', '›']" :slideBy="1"
                                :responsive = "{ 0: { items: 1 }, 576: { items: 2 }, 768: { items: 2 }, 1200: { items: 2 }
                         }"
                                class="vsearch desktop">
                        <div v-for="(job, i) in bprofile.jobs" :key="i">
                          <div class="small">
                            <a :href="'/jobs/detail/'+job.job_slug" class="link-dark text-decoration-none">
                              <div v-if="sliderimage.length > 0">
                                <v-img cover width="200"  v-if="sliderimage.find(image => image.image_type === 1)"
                                  :src="url + sliderimage.find(image => image.image_type === 1).image_url"  height="80"></v-img>
                              </div>
                              <div v-else>
                                <v-img cover width="200" v-for="(image, i) in dummyImages" :key="i" :src=image.src height="80"></v-img>
                              </div>
                            </a>
                            <div class="h160 mt-2">
                              <p class="mb-0 fs12 fw-bold">
                                <a :href="'/jobs/detail/'+job.job_slug" class="link-dark text-decoration-none">
                                  {{ job.job_title }}
                                </a>
                              </p>
                              <div class="fs12 fw-bold">
                              </div>
                              <div class="fs11">
                                <v-icon small>mdi-map-marker</v-icon>{{ job.city }}, {{ job.state }}
                              </div>
                              <div v-if="job.min_exp > 0">
                                <div class="fs11">Experience: {{ job.min_exp }} Years</div>
                              </div>
                              <div v-else>
                                <div class="fs11">Experience: Fresher</div>
                              </div>
                              <div class="fs11">₹{{ job.salary_from }} a Month</div>
                              <div>
                                <a :href="'/jobs/detail/'+job.job_slug" class="cred fs12">Read More</a>
                              </div>
                            </div>
                          </div>
                        </div>
                      </carousel>
                    </div>
                   <div v-else class="text-center">
                     <h5>No Jobs</h5>
                   </div>
                  </b-card-text>
                </b-card-body>
              </b-collapse>
            </b-card>
          </div>
          <div class="accordion" role="tablist">
            <b-card no-body class="mb-1">
              <b-card-header header-tag="header" class="p-1" role="tab">
                <b-button class="w-100 catsy fw-bold d-flex justify-content-between btn-sm"
                          v-b-toggle.accordion-10 variant="light">Sales</b-button>
              </b-card-header>
              <b-collapse id="accordion-10" visible accordion="my-accordion-1" role="tabpanel">
                <b-card-body>
                  <b-card-text>
                    <div v-if="bprofile.allsale.length > 0">
                      <carousel v-if="bprofile.allsale.length > 0" :autoplay="false" :margin="30" :dots="false"
                                :navText="['‹', '›']" :slideBy="1"
                                :responsive = "{ 0: { items: 1 }, 576: { items: 2 }, 768: { items: 2 }, 1200: { items: 2 }
                         }"
                                class="vsearch desktop">
                        <div v-for="(sale, i) in bprofile.allsale" :key="i">
                          <div class="small">
                            <a :href="'/sales/detail/'+sale.sale_slug">
                              <div v-if="sale.sale_imageurl != null">
                                <v-img cover width="200" height="80" :src=(url+sale.sale_imageurl) ></v-img>
                              </div>
                              <div v-else>
                                <v-img :src="('https://dummyimage.com/400x350/fd0606/fff&text=Sale')" cover width="200" height="80"></v-img>
                              </div>
                            </a>
                            <div class="h160 mt-2">
                              <p class="mb-0 fs12 fw-bold">
                                <a :href="'/sales/detail/'+sale.sale_slug" class="text-decoration-none link-dark">
                                {{ sale.sale_title }}
                                </a>
                              </p>
                              <div class="fs12 fw-bold">
                              </div>
                              <div class="cred">Sale
                                <del class="ms-1 h6 ">₹{{ sale.normal_price }}</del>
                                <span class="ms-1 h5 cred">₹{{ sale.sale_price }}</span>
                              </div>
                              <div class="fs11 cred">Validity: <span style="color: black;">{{ sale.saledate_to }} </span></div>
                              <div class="fs11 fw-bold">
                                <v-icon small>mdi-map-marker</v-icon>
                                {{ sale.city }}, {{ sale.state }}
                                <a :href="'/sales/detail/'+sale.sale_slug" class="cred fs12">Read More</a>
                              </div>
                            </div>
                          </div>
                        </div>
                      </carousel>
                    </div>
                   <div v-else class="text-center">
                     <h5>No Sales</h5>
                   </div>
                  </b-card-text>
                </b-card-body>
              </b-collapse>
            </b-card>
          </div>
        </div>
      </v-col>
      <v-col cols="12" sm="12" md="12" lg="8">
        <div class="accordion"  role="tablist">
          <b-card no-body class="mb-1">
            <b-card-header header-tag="header" class="p-1" role="tab">
              <b-button class="w-100 catsy fw-bold d-flex justify-content-between btn-sm"
                        v-b-toggle.accordion-1 variant="light">About Us</b-button>
            </b-card-header>
            <b-collapse id="accordion-1" accordion="my-accordion1" role="tabpanel">
              <b-card-body>
                <b-card-text>
                  <div v-if="bprofile.about">
                    <p v-html="sanitizeText(bprofile.about)" class="text-justify"></p>
                  </div>
                  <div v-else>
                    <p>{{bprofile.name}}</p>
                  </div>
                </b-card-text>
              </b-card-body>
            </b-collapse>
          </b-card>
        </div>
        <div class="accordion" role="tablist">
          <b-card no-body class="mb-1">
            <b-card-header header-tag="header" class="p-1" role="tab">
              <b-button class="w-100 catsy fw-bold d-flex justify-content-between btn-sm"
                        v-b-toggle.accordion-2 variant="light">Services</b-button>
            </b-card-header>
            <b-collapse id="accordion-2"  accordion="my-accordion2" role="tabpanel">
               <b-card-body>
                 <b-card-text v-if="bprofile.services == null" class="text-center">
                   <h6>No Services</h6>
                 </b-card-text>
                 <b-card-text v-else>
                  <ol class="cold2 ps-3" v-if="bprofile.services.service_text != null">
                    <li v-for="(service,index) in bprofile.services.service_text" :key="index">
                      <span v-if="service.length > 0">{{service}}</span>
                      <span v-else>{{bprofile.name}}</span>
                    </li>
                  </ol>
                  <div v-else class="text-center">
                    <h6>No Services</h6>
                  </div>
                 </b-card-text>
               </b-card-body>
            </b-collapse>
          </b-card>
        </div>
        <div class="accordion" role="tablist" v-for="tab in tabsarray" :key="tab.id">
          <b-card no-body class="mb-1">
            <b-card-header header-tag="header" class="p-1" role="tab">
              <b-button class="w-100 catsy fw-bold d-flex justify-content-between btn-sm"
                        v-b-toggle="'accordion-' + tab.id" variant="light">{{ tab.title }}</b-button>
            </b-card-header>
            <b-collapse :id="'accordion-'+tab.id"  accordion="my-accordion3" role="tabpanel">
              <b-card-body>
                <b-card-text>
                  <div v-if="tab.tab_content" v-html="tab.tab_content">
                  </div>
                  <div v-else class="text-center">
                    <h6>No Data</h6>
                  </div>
                </b-card-text>
              </b-card-body>
            </b-collapse>
          </b-card>
        </div>

        <div class="accordion" role="tablist">
          <b-card no-body class="mb-1">
            <b-card-header header-tag="header" class="p-1" role="tab">
              <b-button class="w-100 catsy fw-bold d-flex justify-content-between btn-sm"
                        v-b-toggle.accordion-4 variant="light">Gallery</b-button>
            </b-card-header>
            <b-collapse id="accordion-4"  accordion="my-accordion4" role="tabpanel">
              <b-card-body>
                <b-card-text>
                    <div class="row gal" v-if="bprofile.galleryimage.length > 0">
                        <div class="col-6 col-md-4" v-for="(gallery, index) in bprofile.galleryimage" :key="index">
                            <a :href="url+(gallery.image_url)" data-fancybox="gallery" data-caption="Caption #1">
                                <img :src="url+(gallery.image_url)" class="img-fluid ratio ratio-16x9 rounded-3" alt="image1" />
                            </a>
                            <div v-if="gallery.price != null && gallery.image_title" class="text-center h6">
                                <div>{{gallery.image_title}}</div>
                                <div class="cred">Price - <span style="color: black;">₹{{gallery.price}}</span></div>
                            </div>
                            <div v-else>
                                <div>{{gallery.image_title}}</div>
                            </div>
                        </div>
                    </div>
<!--                  <div v-if="bprofile.galleryimage.length > 0">-->
<!--                      <div class="row">-->
<!--                        <div class="col-6 col-md-4" v-for="(gallery, index) in bprofile.galleryimage" :key="gallery.id">-->
<!--                          <label for="img1" v-if="gallery.image_type == 0" class="d-flex">-->
<!--                            <img :src="url+(gallery.image_url)" class="img-fluid rounded-3 cursor_cls" @click="openModal(index)" alt="gallery" />-->
<!--                          </label>-->
<!--                          <div v-else class="text-center">-->
<!--                            <h6>No Images</h6>-->
<!--                          </div>-->
<!--                          <div v-if="gallery.price != null && gallery.image_title" class="text-center h6">-->
<!--                            <div>{{gallery.image_title}}</div>-->
<!--                            <div class="cred">Price - <span style="color: black;">₹{{gallery.price}}</span></div>-->
<!--                          </div>-->
<!--                          <div v-else>-->
<!--                            <div>{{gallery.image_title}}</div>-->
<!--                          </div>-->
<!--                        </div>-->
<!--                      </div>-->
<!--                  </div>-->
                  <div v-else class="text-center">
                    <h6>No Images</h6>
                  </div>
                </b-card-text>
              </b-card-body>
            </b-collapse>
          </b-card>
        </div>
        <div class="accordion" role="tablist">
          <b-card no-body class="mb-1">
            <b-card-header header-tag="header" class="p-1" role="tab">
              <b-button class="w-100 catsy fw-bold d-flex justify-content-between btn-sm"
                        v-b-toggle.accordion-5 variant="light">Video</b-button>
            </b-card-header>
            <b-collapse id="accordion-5" accordion="my-accordion5" role="tabpanel">
              <b-card-body>
                <b-card-text>
                  <div v-if="bprofile.allvideo.length > 0">
                    <v-row>
                      <v-col cols="12" sm="12" md="4" v-for="(video, i) in bprofile.allvideo" :key="i">
                        <div class="ratio ratio-16x9">
                          <iframe :src=(yemd+video.youtube_id) :title="video.video_title" allowfullscreen></iframe>
                        </div>
                      </v-col>
                    </v-row>
                  </div>
                  <div v-else class="text-center">
                    <h6>No Videos</h6>
                  </div>
                </b-card-text>
              </b-card-body>
            </b-collapse>
          </b-card>
        </div>
        <div class="accordion" role="tablist" id="accordionReview">
          <b-card no-body class="mb-1">
            <b-card-header header-tag="header" class="p-1" role="tab">
              <b-button class="w-100 catsy fw-bold d-flex justify-content-between btn-sm"
                        v-b-toggle.accordion-7 variant="light">Reviews</b-button>
            </b-card-header>
            <b-collapse id="accordion-7" accordion="my-accordion8" role="tabpanel">
              <b-card-body>
                <b-card-text>
                  <div class="text-center fw-bold display-3">{{ bprofile.allreview_avg_rating }}</div>
                  <div class="ratings text-center">
                    <v-rating :value="bprofile.allreview_avg_rating" color="amber" hover half-increments size="24" readonly></v-rating>
                    <div class="small text-center">Based on {{ bprofile.allreview_count }} Reviews</div>
                    <span v-if=" loggedUser == false ">
                      <a href="/user/login">Write a Review</a>
                    </span>
                    <span v-else class="cred">
                      <a v-if="bprofile.user_added_reviewed==null" @click="openReviewModal">Write a Review</a>
                      <a v-else>Reviewed</a>
                    </span>
                  </div>
                  <div class="list-unstyled">
                    <div class="row align-items-center">
                      <div class="col-4 col-md-2 py-2">Excellent:</div>
                      <div class="col-8 col-md-10 py-2">
                        <b-progress height="6px" :value="(count5*100/bprofile.allreview_count)" variant="success" :key="count5"></b-progress>
                      </div>
                    </div>
                    <div class="row align-items-center">
                      <div class="col-4 col-md-2 py-2">Good:</div>
                      <div class="col-8 col-md-10 py-2">
                        <b-progress height="6px" :value="(count4*100/bprofile.allreview_count)" variant="secondary" :key="count4"></b-progress>
                      </div>
                    </div>
                    <div class="row align-items-center">
                      <div class="col-4 col-md-2 py-2">Average:</div>
                      <div class="col-8 col-md-10 py-2">
                        <b-progress height="6px" :value="(count3*100/bprofile.allreview_count)" variant="info" :key="count3"></b-progress>
                      </div>
                    </div>
                    <div class="row align-items-center">
                      <div class="col-4 col-md-2 py-2">Below Average:</div>
                      <div class="col-8 col-md-10 py-2">
                        <b-progress height="6px" :value="(count2*100/bprofile.allreview_count)" variant="warning" :key="count2"></b-progress>
                      </div>
                    </div>
                    <div class="row align-items-center">
                      <div class="col-4 col-md-2 py-2">Poor:</div>
                      <div class="col-8 col-md-10 py-2">
                        <b-progress height="6px" :value="(count1*100/bprofile.allreview_count)" variant="danger" :key="count1"></b-progress>
                      </div>
                    </div>
                  </div>
                  <div class="mt-5">
                    <v-row class="desktop">
                      <v-col cols="4" md="4"></v-col>
                      <v-col cols="4" md="4">
                        <v-autocomplete
                            v-model="selectRatingValue"
                            :items="reviewsStars"
                            item-text="name"
                            item-value="value"
                            @change="filterRating" solo dense label="Review Filter"></v-autocomplete>
                      </v-col>
                      <v-col cols="4" md="4"></v-col>
                    </v-row>
                    <v-row class="mobile">
                      <v-col cols="2" md="4"></v-col>
                      <v-col cols="8" md="4">
                        <v-autocomplete
                            v-model="selectRatingValue"
                            :items="reviewsStars"
                            item-text="name"
                            item-value="value"
                            @change="filterRating" solo dense label="Review Filter"></v-autocomplete>
                      </v-col>
                      <v-col cols="2" md="4"></v-col>
                    </v-row>
                  </div>
                </b-card-text>

                <div v-if="filterReviewData.length > 0">
                  <div class="row">
                    <div class="col-12 col-md-6" v-for="(review,i) in filterReviewData" :key="i">
                      <v-card>
                        <v-card-text>
                          <v-row>
                            <v-col cols="2" md="2">
                              <div v-if="review.user_avatar === null">
                                <v-avatar color="indigo" size="36">
                                  <span class="white--text text-h5">!</span>
                                </v-avatar>
                              </div>
                              <div v-else>
                                <v-avatar>
                                  <img :src="(url+review.user_avatar)" alt="avatar">
                                </v-avatar>
                              </div>
                            </v-col>
                            <v-col cols="7" md="7">
                              <span class="h6 link-dark ms-1">
                                {{ review.name }}
                                <v-rating :value="review.rating" color="amber" dense readonly half-increments size="20">
                                </v-rating>
                              </span>
                            </v-col>
                            <v-col cols="3" md="3">  <span class="small text-muted">  {{ review.days_ago }}</span>
                            </v-col>
                          </v-row>
                          <v-row>
                            <v-col cols="12" md="12"><p class="link-dark mb-0">{{ review.review_text.slice(0, descMaxLength)+'...' }}</p></v-col>
                          </v-row>
                          <div class="row" v-if="review.review_images.length > 0">
                            <v-col v-for="(rvImages,i) in review.review_images" :key="i" cols="4" md="4">
                              <img cover :src="(url+rvImages.review_image_url)" @click="clickedReviewImages(review.review_images, review.review_text)" v-b-modal:model-reviews-image>
                            </v-col>
                          </div>
                        </v-card-text>
                      </v-card>
                    </div>
                  </div>
                </div>
                <div v-else-if="bprofile.allreview.length > 0 && defaultReviewDataShow==true">
                  <div class="row">
                    <div class="col-12 col-md-6" v-for="(review,i) in bprofile.allreview" :key="i">
                      <v-card>
                        <v-card-text>
                          <v-row>
                            <v-col cols="2" md="2">
                              <div v-if="review.user_avatar === null">
                                <v-avatar color="indigo" size="36">
                                  <span class="white--text text-h5">!</span>
                                </v-avatar>
                              </div>
                              <div v-else>
                                <v-avatar>
                                  <img :src="(url+review.user_avatar)" alt="avatar">
                                </v-avatar>
                              </div>
                            </v-col>
                            <v-col cols="7" md="7">
                              <span class="h6 link-dark ms-1">
                                {{ review.name }}
                                <v-rating :value="review.rating" color="amber" dense readonly half-increments size="20">
                                </v-rating>
                              </span>
                            </v-col>
                            <v-col cols="3" md="3">  <span class="small text-muted"> {{ review.days_ago }}</span>
                            </v-col>
                          </v-row>
                          <v-row v-if="review.review_images.length > 0">
                            <v-col cols="12" md="12"><p class="link-dark mb-0">{{ review.review_text.slice(0, descMaxLength)+'...' }}</p></v-col>
                          </v-row>
                          <v-row v-else>
                            <v-col cols="12" md="12" style="height: 132px;"><p class="link-dark mb-0">{{ review.review_text}}</p></v-col>
                          </v-row>
                          <div class="row" v-if="review.review_images.length > 0" >
                            <v-col v-for="(rvImages,i) in review.review_images" :key="i" cols="4" md="4" >
                              <img cover :src="(url+rvImages.review_image_url)" @click="clickedReviewImages(review.review_images, review.review_text)" v-b-modal:model-reviews-image>
                            </v-col>
                          </div>
                        </v-card-text>
                      </v-card>
                    </div>
                  </div>
                </div>
                <div v-else>
                  <div class="row">
                    <div class="col-12 col-md-12 text-center">
                      <h5>No Reviews</h5>
                    </div>
                  </div>
                </div>
              </b-card-body>
            </b-collapse>
          </b-card>
        </div>
        <div class="accordion" role="tablist" id="accordionContact">
          <b-card no-body class="mb-1">
            <b-card-header header-tag="header" class="p-1" role="tab">
              <b-button class="w-100 catsy fw-bold d-flex justify-content-between btn-sm"
                        v-b-toggle.accordion-6 variant="light">Contact</b-button>
            </b-card-header>
            <b-collapse id="accordion-6" accordion="my-accordion6" role="tabpanel">
              <b-card-body>
                <b-card-text>
                  <h2 class="fw-bold">{{bprofile.name}}</h2>
                  <p class="fw-bold">
                    <v-icon>mdi-map-marker</v-icon>
                    {{ bprofile.city }}, {{bprofile.state}}
                  </p>
                  <div class="d-flex justify-content-center justify-content-md-start">
                    <a :href="'tel:+' + bprofile.phonecode + bprofile.mobile_phone">
                      <v-btn color="bgred" class="me-1 text-light" small>
                        <v-icon left> mdi-phone </v-icon>
                      Call Now
                      </v-btn>
                    </a>
                    <a :href="('https://wa.me/'+'+'+bprofile.phonecode+bprofile.mobile_phone+'?text=Enquiry from Bizlx : '+currentURL)" target="_blank" class="text-decoration-none">
                      <v-btn color="success" class="me-1" small>
                        <v-icon left> mdi-whatsapp </v-icon>
                        Whatsapp
                      </v-btn>
                    </a>
                  </div>
                </b-card-text>
              </b-card-body>
            </b-collapse>
          </b-card>
        </div>
        <div id="maps" class="pt-2">
          <iframe
              width="100%"
              height="350"
              frameborder="0" style="border:0"
              referrerpolicy="no-referrer-when-downgrade"
              :src=(mapurl+bname+space+bprofile.city+space+bprofile.state)
              allowfullscreen>
          </iframe>
        </div>
      </v-col>
      </v-row>

      <div class="mobile mt-5">
          <span class="fs-6 fw-bold">Embed Code :</span>
            <v-btn @click="loadjobs" small color="primary" class="ms-1">Jobs</v-btn>
            <v-btn @click="loadhotdeals" small color="primary" class="ms-1">Hot Deals</v-btn>
            <v-btn @click="loadsales" small color="primary" class="ms-1">Sales</v-btn>
            <v-btn @click="loadreviews" small color="primary" class="ms-1">Reviews</v-btn>
      </div>

      <div class="text-end position-relative desktop">
          <span class="fs-6 fw-bold">Embed Code :</span>
            <v-btn @click="loadjobs" small color="primary" class="ms-2">Jobs</v-btn>
            <v-btn @click="loadhotdeals" small color="primary" class="ms-2">Hot Deals</v-btn>
            <v-btn @click="loadsales" small color="primary" class="ms-2">Sales</v-btn>
            <v-btn @click="loadreviews" small color="primary" class="ms-2">Reviews</v-btn>
      </div>

    </v-container>
    <b-modal id="model-gallery1" hide-footer size="lg" @shown="updateActiveSlide">
      <div class="" v-b-modal:lg>
        <carousel v-if="bprofile.galleryimage.length > 0" ref="imageCarousel" :autoplay="false" :margin="30" :items="4" :dots="false" :slideBy="1"
                  :navText="['‹','›']"
                  :responsive="{0:{items:1},576:{items:1},768:{items:1},1200:{items:1}}"
                  :loop="true" :startPosition="activeSlide">
          <div v-for="(gallery, i) in bprofile.galleryimage" :key="i" class="cole">
            <div class="overlay"></div>
            <img :src=(url+gallery.image_url) class="img-fluid top-listing">
            <div class="text-center">
              <div>{{gallery.image_title}}</div>
              <div class="cred" v-if="gallery.price != null">Price - <span style="color: black;">₹{{gallery.price}}</span></div>
            </div>
          </div>
        </carousel>
      </div>
    </b-modal>

    <b-modal id="model-reviews-image" hide-footer >
      <div v-b-modal:lg>
        <carousel v-if="SingleReviewImages.length > 0" :autoplay="false" :margin="30" :items="4" :dots="false" :slideBy="1"
        :navText="['‹','›']"
        :responsive = "{0:{items:1},576:{items:1},768:{items:1},1200:{items:1}}"
        :loop="true">
            <div v-for="(gallery, i) in SingleReviewImages" :key="i" class="cole">
                <div class="overlay"></div>
                <img :src=(url+gallery.review_image_url) class="img-fluid top-listing">
            </div>
        </carousel>
        <div class="text-justify">
          <div>{{Singlereviewtext}}</div>
        </div>
      </div>
    </b-modal>
    <div v-if=" loggedUser == false ">
    </div>
    <div v-else>
      <v-dialog v-model="showModel1" persistent max-width="900">
      <v-card>
        <v-form v-on:submit.prevent="reviewForm" ref="ReviewForm" id="reviewFrm" v-model="valid" lazy-validation>
            <v-card-title class="text-h5 grey lighten-2" style="justify-content:space-between;">Write a Review
                <v-icon  @click="showModel1 = false" class="text-h5">mdi-close</v-icon>
            </v-card-title>
            <v-card-text class="py-0">
                <v-rating v-model="rating" color="yellow darken-3" background-color="grey darken-1" empty-icon="$ratingFull" half-increments hover ></v-rating>
                <div v-if="ratingRules==true" class="reviewCss">Please provide a rating.</div>
                <v-text-field class="py-2"
                    v-model="review"
                    :rules="ReviewRules"
                    :counter="50"
                    label="Please Write Your Review Here">
                </v-text-field>
                <v-row class="py-0">
                  <v-col cols="12" md="6" class="py-0">
                    <span style="color:red">Upload Max 3 images</span>
                    <v-file-input
                        v-model="selectedFiles"
                        multiple
                        show-size
                        @change="handleFileChange">
                    </v-file-input>
                  </v-col>
                  <v-col cols="12" md="6" class="py-0">
                    <div v-for="(file, index) in selectedFilesWithPreview" :key="index" class="image-preview">
                      <img :src="file.preview" alt="Preview" class="preview-image">
                      <v-btn icon @click="deleteFile(index)">
                        <v-icon>mdi-close</v-icon>
                      </v-btn>
                    </div>
                  </v-col>
                </v-row>
            </v-card-text>
            <div class="text-end">
              <v-btn class="my-2 me-3 cred" type="submit" :disabled="!valid">SAVE</v-btn>
              <v-btn @click="showModel1 = false" class="my-2 me-3 cred">CLOSE</v-btn>
            </div>
        </v-form>
      </v-card>
    </v-dialog>
    </div>
    <b-modal id="model-login" title="Login" hide-footer>
      <form ref="form" @submit.stop.prevent="">
        <div class="mb-2 login-imge">
          <img src="https://bizlx.itechvision.in/images/login-img.png" class="login-image">
        </div>
        <div class="py-2">
          <b-form-input type="email" id="email" v-model="name" required placeholder="Enter email"></b-form-input>
        </div>
        <div class="py-2">
          <b-form-input type="password" id="password" v-model="name" required placeholder="password"></b-form-input>
        </div>
        <div class="mt-3 singup-button">
          <a href="/business/register">
            <b-button variant="success" type="submit">Submit</b-button>
          </a>
          <p> Don't have an account?
            <a href="/user/register" class="cred">Sign up</a>
          </p>
        </div>
      </form>
    </b-modal>
    <v-dialog v-model="dialog1" max-width="500px">
      <v-card>
        <v-card-title style="justify-content:space-between;" class="text-h6 grey lighten-2">
          <span class="text-h5">Send Enquiry</span>
          <v-icon  @click="dialog1 = false" class="text-h5">mdi-close</v-icon>
        </v-card-title>
        <v-card-text>
          <!-- <v-container>
            <v-form ref="form1" v-model="valid">
              <input type="hidden" id="business_user_id" ref="business_user_id" :value=bprofile.id>
              <input type="hidden" id="logged_user_id" ref="logged_user_id" :value=loggedUser.id>
              <v-row>
                <v-col cols="12" sm="6" md="6" class="p-1">
                  <v-text-field dense type="text" label="Name" :rules="userRules" v-model="name" required></v-text-field>
                </v-col>
                <v-col cols="12" sm="6" md="6" class="p-1">
                  <v-text-field dense type="email" label="Email" :rules="emailRules" v-model="email"></v-text-field>
                </v-col>
                <v-col cols="12" sm="6" md="12" class="p-1">
                  <v-text-field dense type="number" label="Phone" :rules="mobileRules" v-model="phone" required></v-text-field>
                </v-col>
                <v-col cols="12" sm="6" md="12" class="p-1">
                  <v-textarea dense type="text" rows="2" label="Description" :rules="textareaRules" v-model="textarea" required></v-textarea>
                </v-col>
              </v-row>
              <div class="mt-5 text-end">
                <v-btn class="ms-3" color="success" @click="getAllData" :disabled="!valid">SUBMIT</v-btn>
              </div>
            </v-form>
          </v-container> -->
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-dialog v-model="MessageModel1" persistent max-width="500">
      <v-card>
        <v-col cols="12" md="12" class="d-flex" style="justify-content: space-between;">
          <div class="mt-1 text-h6">Inquiry sent successfully.
          </div>
          <div @click="MessageModel1 = false" class="my-2">
            <v-icon>mdi-close</v-icon>
          </div>
        </v-col>
      </v-card>
    </v-dialog>
    <v-dialog v-model="MessageModel2" persistent max-width="500">
      <v-card>
        <v-card-title style="justify-content:space-between;" class="text-h6 grey lighten-2">
          <span class="text-h5">Copy Code To Website</span>
          <v-icon  @click="MessageModel2 = false" class="text-h5">mdi-close</v-icon>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-form>
              <v-row>
                <v-col cols="12" md="12" class="d-flex" style="justify-content: space-between;">
                  <v-textarea rows="3" label="Embed Code" v-model="iframeSrc"></v-textarea>
                </v-col>
              </v-row>
            </v-form>
          </v-container>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-dialog v-model="MessageModel3" persistent max-width="500">
      <v-card>
        <v-card-title style="justify-content:space-between;" class="text-h6 grey lighten-2">
          <span class="text-h5">Copy Code To Website</span>
          <v-icon  @click="MessageModel3 = false" class="text-h5">mdi-close</v-icon>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-form>
              <v-row>
                <v-col cols="12" md="12" class="d-flex" style="justify-content: space-between;">
                  <v-textarea rows="3" label="Embed Code" v-model="iframeSrc"></v-textarea>
                </v-col>
              </v-row>
            </v-form>
          </v-container>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-dialog v-model="livelocationModel" persistent max-width="500">
      <v-card>
        <v-card-title style="justify-content:space-between;" class="text-h6 grey lighten-2">
          <span class="text-h5">Copy Url To Share:</span>
          <v-icon  @click="livelocationModel = false" class="text-h5">mdi-close</v-icon>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-form>
              <v-row>
                <v-col cols="12" md="12" class="d-flex" style="justify-content: space-between;">
                  <v-textarea rows="3" label="Live location url" v-model="locationurl" disabled></v-textarea>
                </v-col>
              </v-row>
            </v-form>
          </v-container>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-dialog v-model="MessageModel4" persistent max-width="500">
      <v-card>
        <v-card-title style="justify-content:space-between;" class="text-h6 grey lighten-2">
          <span class="text-h5">Copy Code To Website</span>
          <v-icon  @click="MessageModel4 = false" class="text-h5">mdi-close</v-icon>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-form>
              <v-row>
                <v-col cols="12" md="12" class="d-flex" style="justify-content: space-between;">
                  <v-textarea rows="3" label="Embed Code" v-model="iframeSrc"></v-textarea>
                </v-col>
              </v-row>
            </v-form>
          </v-container>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-dialog v-model="MessageModel5" persistent max-width="500">
      <v-card>
        <v-card-title style="justify-content:space-between;" class="text-h6 grey lighten-2">
          <span class="text-h5">Copy Code To Website</span>
          <v-icon  @click="MessageModel5 = false" class="text-h5">mdi-close</v-icon>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-form>
              <v-row>
                <v-col cols="12" md="12" class="d-flex" style="justify-content: space-between;">
                  <v-textarea rows="3" label="Embed Code" v-model="iframeSrc"></v-textarea>
                </v-col>
              </v-row>
            </v-form>
          </v-container>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-snackbar v-model="snackbar" :timeout="timeout">{{ text }}
        <template v-slot:action="{}">
          <v-btn color="blue" text @click="snackbar = false">Close</v-btn>
        </template>
    </v-snackbar>
    </div>
</template>
<script>
import carousel from "vue-owl-carousel";
import axios from "axios";
export default {
  name: "BusinessView",
  props: {
      id: Number,
      bdata:Object,
  },
  components: { carousel },
  data() {
    return {
      htmlText: /<[^>]+>/g,
      space:' ',
      picd:'https://source.unsplash.com/random/400x200?sig=',
      picm:'https://source.unsplash.com/random/200x200?sig=',
      activeSlide: 0, // Default to the first image
      mapurl:"https://www.google.com/maps/embed/v1/place?key=AIzaSyD1cGNhJz2BiG4oODjDAkfOH__dxXC_N10&q=",
      shareurl:'https://bizlx.com/',
      select: '',
      reviewsStars: [
        { name: 'All Reviews', value: 6 },
        { name: '5 Stars', value: 5 },
        { name: '4 Stars', value: 4 },
        { name: '3 Stars', value: 3 },
        { name: '2 Stars', value: 2 },
        { name: '1 Star', value: 1 },
      ],
      business_id: '',
      descMaxLength: 100,
      iframeSrc: '',
      embed_code: '',
      MessageModel1: false,
      MessageModel2: false,
      MessageModel3: false,
      livelocationModel: false,
      MessageModel4: false,
      MessageModel5: false,
      dialog1: false,
      cycle: true,
      yemd:'https://www.youtube.com/embed/',
      index: null,
      bprofile: [],
      bname: '',
      allhotdeal: {},
      showModel1: false,
      rating: null,
      review: '',
      count1: '',
      count2: '',
      count3: '',
      count4: '',
      count5: '',
      dialog: false,

      itemsPerRow:2,
      name:'',
      email:'',
      phone:'',
      textarea:'',
      valid: false,
      direction: "right",
      direction1: "bottom",
      fab: false,
      url:'https://bizlx.s3.ap-south-1.amazonaws.com',
      fling: true,
      hover: true,
      tabs: null,
      transition: "scale-transition",
      panel1: 0,
      panel2: 0,
      disabled: false,
      loggedUser: false,
      userID: null,
      sliderimage: [],
      tabsarray: [],
      inquiryMessage:'',
      timeout:-1,
      snackbar:false,
      text:'',
      locationurl:'',
      isLiked: false,
      userRules: [
        v => !!v || 'Name is required.',
        v => v?.length <= 50 || 'Name must be less than 50 characters.',
      ],
      textareaRules: [
        v => !!v || 'Description is required.',
        v => v?.length <= 200 || 'Name must be less than 200 characters.',
      ],
      mobileRules: [
        v => !!v || 'Mobile number is required.',
        v => v?.length != 9 ||  'Mobile number must be 10 characters.',
      ],
      emailRules: [
        v => !!v || 'E-mail is required.',
        v => /.+@.+/.test(v) || 'E-mail must be valid.',
      ],
      dummyImages: [
        {id:1,src: "https://bizlx.itechvision.in/images/banner.jpg",}
      ],
      ReviewRules: [
        v => !!v || 'Review Description is required.',
        v => v?.length <= 50 ||'Description will be 50 characters.',
      ],
      alloutUploadImage: 3,
      multipleFilesUploadByAPI: [],
      selectedFilesWithPreview: [],
      selectedFiles: [],
      ratingRules: false,
      selectRatingValue: 6,
      filterReviewData: [],
      defaultReviewDataShow: true,
      SingleReviewImages: [],
      Singlereviewtext: '',
      currentURL:'',
      uname:'',
      heartColor:'',

    };
  },
  created(){
    if(this.isAuthenticated()){
      this.loggedUser = this.isAuthenticated();
      this.userID = this.$store.state.userData.id;
    } else {
      this.loggedUser = false;
    }
    this.currentURL = this.getCurrentURL();
    this.uname = this.bdata.username;
  },
  mounted() {
    this.businessprofile();
  },
  methods:{
    getCurrentURL() {
      return window.location.href;
    },
    sanitizeText(text) {
      // Replace HTML tags with an empty string
      return text.replace(this.htmlText, '');
    },
    handleFileChange() {
      this.selectedFilesWithPreview = [];
      this.multipleFilesUploadByAPI = [];
      for (let i = 0; i < this.alloutUploadImage; i++) {
        const file = this.selectedFiles[i];
        const reader = new FileReader();
        reader.onload = (event) => {
          this.selectedFilesWithPreview.push({
            file: file,
            preview: event.target.result
          });
        };
        reader.readAsDataURL(file);
        this.multipleFilesUploadByAPI.push(this.selectedFiles[i]); // Multiple file upload for api
      }
    },
    deleteFile(index) {
      this.selectedFiles.splice(index, 1);
      this.selectedFilesWithPreview.splice(index, 1);
    },

    shareOnFacebook() {
      const url = this.shareurl+this.uname;
      window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`, '_blank');
    },
    shareOnWhatsApp() {
      // Create the WhatsApp share URL
      const url = this.shareurl+this.uname;
      const whatsappShareUrl = `https://api.whatsapp.com/send?text=${encodeURIComponent(url)}`;
      window.open(whatsappShareUrl, '_blank');
    },

    embedhotdealshareOnFacebook() {
      const url = `https://bizlx.com/hotdealsby/business/${this.business_id}`;
      window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`, '_blank');
    },
    embedsalesshareOnFacebook() {
      const url = `https://bizlx.com/salesby/business/${this.business_id}`;
      window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`, '_blank');
    },
    embedjobsshareOnFacebook() {
      const url = `https://bizlx.com/jobsby/business/${this.business_id}`;
      window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`, '_blank');
    },
    embedreviewshareOnFacebook() {
      const url = `https://bizlx.com/reviewsby/business/${this.business_id}`;
      window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`, '_blank');
    },

    loadhotdeals() {
      // Construct the URL with the ID
      this.iframeSrc = `<iframe src='https://bizlx.com/hotdealsby/business/${this.uname}' width="100%" height="400px"></iframe>`;
      this.MessageModel2 = true;
    },
    loadjobs() {
      // Construct the URL with the ID
      this.iframeSrc = `<iframe src='https://bizlx.com/jobsby/business/${this.uname}' width="100%" height="400px"></iframe>`;
      this.MessageModel3 = true;
    },
    loadLocation() {
      var url = null;
      if( this.bprofile.live_location == '' ){
        url = ''; // Replace with the URL you want to open in a new tab
        window.open(url, '_blank');
      }else{
        url = this.bprofile.live_location;
        window.open(url, '_blank');
      }
    },
    loadsales() {
      // Construct the URL with the ID
      this.iframeSrc = `<iframe src='https://bizlx.com/salesby/business/${this.uname}' width="100%" height="400px"></iframe>`;
      this.MessageModel4 = true;
    },
    loadreviews() {
      // Construct the URL with the ID
      this.iframeSrc = `<iframe src='https://bizlx.com/reviewsby/business/${this.uname}' width="100%" height="400px"></iframe>`;
      this.MessageModel5 = true;
    },

    businessprofile(){
      axios.get('/api/businesses/profile/'+this.bdata.username+'/'+this.userID)
          .then((resp) =>{
            this.bprofile = resp.data.businessprofile;
            var rep = resp.data.businessprofile.name;
            this.bname = rep.replace('&','and');
            this.business_id = resp.data.businessprofile.id;
            this.business_name = resp.data.businessprofile.username;
            this.imageurl = resp.data.businessprofile.user_avatar;
            this.allhotdeal = resp.data.allhotdeal;
            this.count1 = resp.data.count1;
            this.count2 = resp.data.count2;
            this.count3 = resp.data.count3;
            this.count4 = resp.data.count4;
            this.count5 = resp.data.count5;
            this.tabsarray = resp.data.tabs;
            this.sliderimage = resp.data.coverimages;
              if(resp.data.businessprofile.user_business_wishlist!=null){
                this.isLiked = 'red';
                this.heartColor = 'red';
              }
          })
    },

    // toggleLike() { 
    //   if(this.loggedUser == false ){
    //       this.snackbar = false;
    //       window.location.href = '/user/login';
    //       this.timeout = 3000;
    //   }else{
    //     this.isLiked = !this.isLiked;
    //     let data = {
    //       business_id: this.bprofile.id,
    //       wishlist_type: 5,
    //       services_id: this.bprofile.id,
    //       token:localStorage.getItem('userToken'),
    //       // business_id: this.bprofile.business_id,
    //       // wishlist_type: 5,
    //       // services_id: this.bprofile.id,
    //     };
    //     if(this.isLiked == true){ // add
    //         axios.post("/api/add/wishlist", data)
    //             .then((resp) => {
    //                   this.snackbar = true;
    //                   this.text = resp.data.message;
    //                   this.timeout = 3000;
    //                   this.isLiked = 'red';
    //                   this.heartColor = 'red';
    //                   this.businessprofile();
    //             }
    //         );
    //     }
    //     if(this.isLiked == false){ // remove
    //         axios.post("/api/add/wishlist", data)
    //             .then((resp) => {
    //                   this.snackbar = true;
    //                   this.text = resp.data.message;
    //                   this.timeout = 3000;
    //                   this.isLiked = '';
    //                   this.heartColor = 'white';
    //                   this.businessprofile();
    //             }
    //         );
    //     }
    //   }
    // },

    toggleLike() {
  const token = localStorage.getItem('userToken');
  if (!token) {
    this.snackbar = false;
    window.location.href = '/user/login';
    return;
  }

  this.isLiked = !this.isLiked;
  let data = {
    business_id: this.bprofile.id,
    wishlist_type: 5,
    services_id: this.bprofile.id,
    token,
  };

  axios.post("/api/add/wishlist", data)
    .then((resp) => {
      this.snackbar = true;
      this.text = resp.data.message;
      this.timeout = 3000;
      this.heartColor = this.isLiked ? 'red' : 'white';
      this.businessprofile();
    })
    .catch((error) => {
      console.error('Error:', error);
    });
},

    filterRating(){ // Review fielter
      this.filterReviewData = [];
      for (var k = 0; k < this.bprofile.allreview.length; k++) { // filter like 1-5
        if(this.selectRatingValue == this.bprofile.allreview[k].rating){
          this.filterReviewData.push(this.bprofile.allreview[k]);
        }
        if(this.selectRatingValue+'.'+5 == this.bprofile.allreview[k].rating){ // filter like 1.5-5
          this.filterReviewData.push(this.bprofile.allreview[k]);
        }
        if(this.selectRatingValue == 6){  // filter all if select (All Reviews) option
          this.filterReviewData.push(this.bprofile.allreview[k]);
        }
      }
      this.defaultReviewDataShow=false;
    },
    openModal(index) {
      this.activeSlide = index;
      this.$bvModal.show('model-gallery1');
    },
    updateActiveSlide() {
      // Set the active slide on the carousel using the ref
      this.$nextTick(() => {
        this.$refs.imageCarousel.goToPage(this.activeSlide);
      });
    },
    clickedReviewImages(reviewImages,reviewtext){ // get images of single reviews & set on modal
     this.SingleReviewImages = reviewImages;
     this.Singlereviewtext = reviewtext;
    },
    // getAllData(){
    //   var data = {
    //     name: this.name,
    //     email: this.email,
    //     phone: this.phone,
    //     textarea: this.textarea,
    //     business_user_id: this.$refs.business_user_id.value,
    //     logged_user_id: this.$refs.logged_user_id.value,
    //   };
    //   axios.post("inquirey", data)
    //   .then((resp) => {
    //     if(resp.data.success == true)
    //       this.$refs.form1.reset();
    //       this.dialog1 = false;
    //       this.MessageModel1 = true;
    //   });
    //   this.$refs.form1.validate();
    // },
    openReviewModal() {
      this.showModel1 = true;
    },
    reviewForm(){
      if (this.rating == null) {
        this.ratingRules = true;
      }else{
        const config = {
          headers: { 'content-type': 'multipart/form-data' }
        }
       const userData =  localStorage.getItem('userData');
       const user = JSON.parse(userData);
        var reviewData = {
          review_business_id: this.bprofile.id,
          review_user_id: user.id,
          rating: this.rating,
          description: this.review,
          review_imgs: this.multipleFilesUploadByAPI,
        };
        axios.post("/api/add/business/review", reviewData, config)
          .then((resp) => {
            if(resp.data.success == true){
              this.showModel1 = false;
              this.snackbar = true;
              this.text = resp.data.message;
              this.timeout = 3000;
              this.ratingRules = false;
              this.businessprofile();
              this.$refs.ReviewForm.reset();
            }
        });
      }
    },
    isAuthenticated() {
      return this.$store.state.isAuthenticated;
    },
    user_role() {
      return this.$store.state.userData.user_role;
    },
},
computed: {
    selectedFilesCount() {
      return this.formData.selectedFiles.length;
    },
    catGroups () {
      return Array.from(Array(Math.ceil(this.bprofile.allhotdeal.length / this.itemsPerRow)).keys())
    },

  },
};
</script>
<style scoped>
.bcenter .v-image__image {
    background-position: bottom center !important;
}

.cursor_cls{
  cursor: pointer;
}
img.cursor_cls {
    height: 100%;
    aspect-ratio: 16/9;
    background:var(--bs-dark);
    }
.v-application .wishlist-icon .white--text {
  color: rgba(0, 0, 0, 0.54) !important;
}

img{
  max-width:100%;
  height:auto;
  }
.gal img {
    aspect-ratio: 16/9;
    object-fit: contain;
    background: rgba(0,0,0,0.2);
    }
.cole .img-fluid.top-listing {
  aspect-ratio: 16/9;
  object-fit: contain;
}
.position-relative {
  position: relative!important;
  bottom: -76px!important;
  width: 50%;
  left: 550px;
  }
.gallery{
  position:fixed;
  top:0;
  left:-9999px;
  width:100%;
  height:100%;
  background:rgba(0,0,0,.9);
  text-align:center;
  }
#img1:checked ~ .gallery,
#img2:checked ~ .gallery,
#img3:checked ~ .gallery{
  left: 0;
  }
.gallery h2,
.gallery p{
  padding:8px 0;
  color:#fff;
  }
.gallery img{
  width:auto;
  height:60vh;
}
.image-preview {
  display: inline-block;
  margin-right: 10px;
}
.preview-image {
  width: 60px;
  height: 60px;
  object-fit: cover;
}
.reviewCss{
  font-size: 12px;
  color: #ff5252 !important;
}
/* .item{
  position:absolute;
  top:0;
  left:0;
  width:100%;
  height:100%;
  display:none;
  } */
.pager {
  position: absolute;
  top: 30%;
  left:1%;
  display:flex;
  width: 98%;
  }
.pager label{
  padding:8px;
  background:#fff;
  }
.gallery img {
  margin-top: 30px;
  }
.pager label.prev{margin-right:auto}
.pager label.next{margin-left:auto}
#close{
  position: absolute;
  top: 8px;
  right: 8px;
  padding:12px 8px;
  color:#fff;
  }
#img1:checked ~ .gallery #gal1{display:block}
#img2:checked ~ .gallery #gal2{display:block}
#img3:checked ~ .gallery #gal3{display:block}

label.next::after {
  content: "\f105";
  font-family: FontAwesome;
  font-size: 50px;
  cursor: pointer;
  }
label#close::after {
  content: "\f00d";
  font-family: FontAwesome;
  font-size: 30px;
  cursor: pointer;
  }
label.prev::before {
  content: "\f104";
  font-family: FontAwesome;
  font-size: 50px;
  cursor: pointer;
  }
span.write-a-review a {
  color: #fd0606;
  font-weight: 500;
  text-decoration: none !important;
}

span.write-a-review {
  position: relative;
  bottom: 0;
  font-size: 17px;
}
.section {
  background: #f1f3f4;
  padding-top: 30px;
  padding-bottom: 30px;
}
.fs12{font-size: 12px;}
.fs11{font-size: 11px;}
img.login-image {
    width: 100%;
    max-width: 130px;
    border-radius: 100px;
}
.login-imge {
    text-align: center;
}
.singup-button {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.item.mitem {
  box-shadow: 0 6px 10px -6px rgba(0,0,0,0.5);
  background: #f1f2f380;
  padding: 7px;
}
.mdetails {
  padding: 7px 0 0 7px;
}
.mimg {
  width: 110px;
}
.mtitle {
  font-weight: 600;
}
.maddress {
  font-size: 12px;
}
.mdetails {
  width: 100%;
}
.mcontact {
  width: 42px;
}
.v-expansion-panel--active > .v-expansion-panel-header {
	min-height: 48px;
}
.enq {
	position: fixed;
	top: 33%;
	transform: rotate(-90deg);
	left: -17px;
	z-index: 2;

}
.btn.catsy.collapsed::after {
  content: '+';
  color: #000;
  }
.btn.catsy.not-collapsed::after {
  content: '-';
  color: #000;
  }
@media(max-width: 576px) {
  .row.nosales {
    width: 100% !important;
}

  .v-speed-dial--direction-bottom .v-speed-dial__list {
    flex-direction: unset;
    top: 40%;
    }
  .v-speed-dial--direction-top .v-speed-dial__list, .v-speed-dial--direction-bottom .v-speed-dial__list {
    left: -58px;
    width: 100%;
    }
    .fbicon {
    margin-left: 5px;
    margin-right: 5px;
}
.mobile .sc1 > a{
  width: 55px;
}
  }
@media (min-width: 577px) {
   .cold2 {
     column-count: 2;
    }
  .cold2 > li{
    margin-right: 10px;
  }
  }
.theme--light.v-btn.v-btn--disabled.v-btn--has-bg {
  background-color: #4caf50 !important;
  border-color: #4caf50 !important;
  color:#fff !important;
}

.list.list-unstyled.cold2 ul {
	padding-left: 24px;
}
a {
  text-decoration: none;
}
</style>
