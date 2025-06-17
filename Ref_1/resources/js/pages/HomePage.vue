<template>
    <v-container>
        <v-row>
            <v-col cols="6">
                <h5 class="cblu"><span class="bbred">Popular</span></h5>
            </v-col>
            <v-col cols="6" class="text-end">
                <a href="/category/all" class="h5 cblu text-end">All</a>
            </v-col>
        </v-row>
        <v-row>
            <v-col cols="12">
                <carousel v-if="subcats.length > 0" :autoplay="false" :margin="10" :dots="false" :slideBy="2" :loop="true"
                          :navText="['‹','›']"
                          :responsive = "{0:{items:4},576:{items:4},768:{items:4},1200:{items:8}}"
                          class="cats mobile">
                    <div v-for="(group, i) in subcatGroups" :key="i">
                        <div class="item" v-for="cat in subcats.slice(i * itemsPerRow3, (i + 1) * itemsPerRow3)" :key="cat.id" @click="sendSearch(cat.id,cat.subcat_name,true)">
                            <div>
                                <img :src=(url+cat.subcat_img_url) width="40" height="40" :alt=cat.subcat_name>
                                <p class="category small">{{cat.subcat_name}}</p>
                            </div>
                        </div>
                    </div>
                </carousel>
                <carousel  v-if="subcats.length > 0"  :margin="20" :dots="false" :slideBy="2" :loop="false"
                           :navText="['‹','›']"
                           :responsive = "{0:{items:4},576:{items:4},768:{items:5},992:{items:6},1200:{items:8}}"
                           class="cats desktop">
                    <div v-for="(group, i) in catGroups" :key="i">
                        <div class="item dd" v-for="cat in subcats.slice(i * itemsPerRow, (i + 1) * itemsPerRow)" :key="cat.id"
                             @click="sendSearch(cat.id,cat.subcat_name)" :title="cat.subcat_name">
                                <img :src=(url+cat.subcat_img_url) width="40" height="40" :alt=cat.subcat_name>
                                <p class="category small">{{cat.subcat_name}}</p>
                        </div>
                    </div>
                </carousel>
            </v-col>
        </v-row>
        <v-row>
            <v-col cols="12">
                <h5 class="cblu">
                    <span class="bbred">Top Ads</span>
                </h5>
            </v-col>
            <v-col cols="12">
                <div class="ads">
                    <carousel v-if="ads.length > 0" :autoplay="false" :margin="15" :items="4" :dots="false" :slideBy="1"
                              :navText="['‹','›']"
                              :responsive = "{0:{items:2},576:{items:2},768:{items:3},1200:{items:4}}"
                              :loop="true">
                        <div v-for="ad in ads" :key="ad.id" class="cole">
                            <a :href="ad.business_uname" class="text-decoration-none">
                                <v-card>
                                    <v-img :lazy-src="(url+ad.ad_img_url)" :src=(url+ad.ad_img_url) :alt=ad.business_name height="160"
                                        class="white--text align-end"
                                        gradient="to bottom, rgba(0,0,0,0.01), rgba(0,0,0,.6)"
                                    >
                                        <div class="p-2">
                                            <div class="adtitle">{{ ad.business_name }}</div>
                                            <div class="loc">
                                                <v-icon small color="conblue">mdi-map-marker</v-icon>
                                                {{ ad.city }}, {{ ad.state }}
                                            </div>
                                        </div>
                                    </v-img>
                                </v-card>
                            </a>
                        </div>
                    </carousel>
                </div>
            </v-col>
        </v-row>
        <v-row>
            <v-col cols="6">
                <h5 class="cblu"><span class="bbred">Jobs</span></h5>
            </v-col>
            <v-col cols="6">
                <h5 class="cblu text-end">
                    <a href="/jobs/all-cities">All</a></h5>
            </v-col>
        </v-row>
        <v-row>
            <v-col cols="12">
                <carousel v-if="jobs.length > 0" :autoplay="false" :margin="30" :items="6" :dots="false" :slideBy="1" :loop="true"
                          :navText="['‹','›']"
                          :responsive = "{0:{items:4},576:{items:4},768:{items:4},992:{items:5},1200:{items:6}}"
                          class="find-job">

                    <div v-for="job in jobs" :key="job.id" class="item text-center">
                        <a class="link-dark text-decoration-none" :href="'jobs/cat-city/'+job.job_cat_slug">
                            <img :src=url+job.job_img_url :alt=job.job_cat_name class="img-fluid" width="32" height="32">
                            <p class="small job-category">{{job.job_cat_name}}</p>
                        </a>
                    </div>
                </carousel>
            </v-col>
        </v-row>
        <v-row>
            <v-col cols="12">
                <h5 class="cblu">
                    <span class="bbred">City Deals</span>
                </h5>
            </v-col>
            <v-skeleton-loader v-if="loading" :loading="loading" type="card" />
            <v-col v-else cols="12">
<carousel
  v-if="slides.length > 0"
  :margin="10"
  :navText="['‹','›']"
  :responsive="{
    0: { items: 2 },
    576: { items: 2 },
    768: { items: 2.3 },
    992: { items: 3 },
    1200: { items: 3.5 }
  }"
  :dots="false"
  class="city"
>
  <div class="item" v-for="city in slides" :key="city.id">
    <v-img
      :lazy-src="url + city.city_img_url"
      :src="url + city.city_img_url"
      height="160"
      
      width="100%"
      cover
      gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.5)"
      class="rounded"
    >
      <!-- Listings Button -->
      <span class="listings position-absolute">
        <span @click="showListing(city.city_id, city.city)">
          <v-btn small color="error">
            Listings
            <span class="badge bg-white text-black ms-1">{{ city.listcount }}</span>
          </v-btn>
        </span>
      </span>

      <!-- Deals Button -->
      <span class="deals position-absolute">
        <span v-if="city.dealcount !== 0" @click="showDeals(city.city_id, city.city)">
          <v-btn small color="error">
            Deals
            <span class="badge bg-white text-black ms-1">{{ city.dealcount }}</span>
          </v-btn>
        </span>
        <span v-else>
          <v-btn small title="No Deals" color="error">No Deals</v-btn>
        </span>
      </span>

      <!-- City Name -->
      <span class="place position-absolute">
        <v-btn block color="transparent" class="text-white fw-bolder">{{ city.city }}</v-btn>
      </span>
    </v-img>
  </div>
</carousel>
</v-col>
</v-row>

        <v-row class="desktop pb-3" v-for="cats in catslider" :key="cats.id">
            <div class="hometabs px-3 px-md-0">
                <h5 class="home-service cblu mb-3">
                    <span class="bbred">{{cats.subcat_name}}</span>
                </h5>
            </div>
            <b-tabs lazy content-class="p-0" class="px-0" pills active-nav-item-class="fw-bold bgred mb-2 text-white"
                    nav-class="fw-bold mtab bootabs">
                <b-tab title="Info" active>
                    <b-card-text>
                        <v-card flat>
                            <v-card-text class="p-0">
                                <div v-if="cats.info.length > 0">
                                    <carousel :autoplay="false" :nav="true"
                                              :margin="30" :items="4" :dots="false" :slideBy="1" :navText="['‹','›']"
                                              :responsive = "{0:{items:1},576:{items:2},768:{items:2.3},992:{items:3},1200:{items:4}}" class="hotels desktop">
                                        <div v-for="hotel in cats.info" :key="hotel.id" class="item">
                                            <v-card>
                                                <a :href="'/'+hotel.business_uname" class="link-dark text-decoration-none">
                                                    <div v-if="hotel.mainimage != null">
                                                        <v-img cover :src=(url+hotel.mainimage) max-height="150" :aspect-ratio="16/9"></v-img>
                                                    </div>
                                                    <div v-else>
                                                        <v-img cover :src="('https://dummyimage.com/299x150/969096/3f4042.jpg&text='+hotel.first_last_letter)" height="150"></v-img>
                                                    </div>
                                                </a>
                                                <a class="mt-2 p-2 d-flex link-dark" :href="'/'+hotel.business_uname" >
                                                    <div class="lt w-100">
                                                        <a :href="'/'+hotel.business_uname" class="link-dark text-decoration-none">
                                                            <h6 style="line-height:22px;max-height: 22px;overflow: hidden;">{{ hotel.name }}</h6>
                                                        </a>
                                                        <div class="mreviews d-flex align-items-center">
                                                            <v-rating dense color="warning" size="15" length="5" :value=hotel.rating readonly></v-rating><span class="badge bg-dark ms-2">{{ hotel.rating }}</span>
                                                        </div>
                                                        <div style="min-height: 44px">
                                                            <v-icon small>mdi-map-marker</v-icon>
                                                            {{hotel.city}}, {{hotel.state}}
                                                        </div>
                                                    </div>
                                                    <div class="mcontact">
                                                        <a :href="('https://wa.me/'+hotel.mobile_phone+'?text=Enquiry from Bizlx : '+currentURL)" target="_blank">
                                                            <v-btn fab x-small color="success">
                                                                <v-icon>mdi-whatsapp</v-icon>
                                                            </v-btn>
                                                        </a>
                                                        <a :href="('tel:'+ hotel.mobile_phone)" target="_blank">
                                                            <v-btn fab x-small color="primary" class="my-2">
                                                                <v-icon>mdi-phone</v-icon>
                                                            </v-btn>
                                                        </a>
                                                        <a :href="'/'+hotel.business_uname">
                                                            <v-btn fab x-small color="info">
                                                                <v-icon>mdi-eye-outline</v-icon></v-btn>
                                                        </a>
                                                    </div>
                                                </a>
                                            </v-card>
                                        </div>
                                    </carousel>
                                </div>
                                <div v-else class="text-center">
                                    <div>No Business in {{cats.subcat_name}}</div>
                                </div>
                            </v-card-text>
                        </v-card>
                    </b-card-text>
                </b-tab>
                <b-tab title="Hot Deals">
                    <b-card-text>
                        <v-card flat>
                            <v-card-text class="p-0">
                                <div v-if="cats.hotdeal.length > 0">
                                    <carousel :autoplay="false" :margin="30" :dots="false" :navText="['‹', '›']" :slideBy="1"
                                              :responsive = "{0:{items:1},576:{items:2},768:{items:2.3},992:{items:3},1200:{items:4}}" class="hotdels">
                                        <a v-for="(deal, i) in cats.hotdeal" :key="i" class="item link-dark"
                                                     :href="'/deals/detail/'+deal.hotdeal_slug">
                                            <v-card>
                                                <div v-if="deal.hotdeal_image_url != ''">
                                                    <a :href="'/deals/detail/'+deal.hotdeal_slug" class="link-dark text-decoration-none">
                                                        <v-img cover :src=(url+deal.hotdeal_image_url) height="150"></v-img>
                                                    </a>
                                                </div>
                                                <div v-else class="text-center">
                                                    <a :href="'/deals/detail/'+deal.hotdeal_slug" class="link-dark text-decoration-none">
                                                        <v-img cover :src="('https://dummyimage.com/299x150/969096/3f4042.jpg&text='+deal.first_last_letter)" height="150"></v-img>
                                                    </a>
                                                </div>
                                                <div class="mt-2 p-2 d-flex">
                                                    <div class="lt w-100">
                                                        <p class="mb-1 fw-bold" style="max-height: 22px;overflow: hidden;">
                                                            <a :href="'/deals/detail/'+deal.hotdeal_slug" class="link-dark text-decoration-none">
                                                                {{ deal.hot_deal_title }}
                                                            </a>
                                                        </p>
                                                        <div>
                                                            By: <a :href="'/'+deal.business_uname">
                                                            {{ deal.name }}
                                                        </a>
                                                        </div>
                                                        <!-- <div v-if="!loggedUser" class="fw-bold">Add To Wishlist
                                                          <v-icon>mdi-heart-outline</v-icon>
                                                        </div>
                                                        <div v-else class="fw-bold">Add To Wishlist
                                                          <v-icon v-if="deal.user_added_wishlist!=null" color="red" small>mdi-heart</v-icon>
                                                          <v-icon v-else small>mdi-heart-outline</v-icon>
                                                        </div> -->
                                                        <p class="mb-0 cred">Price:
                                                            <span style="color: black;">
                                                               ₹{{ deal.price_to }}
                                                            </span>
                                                        </p>
                                                        <p class="mb-0 cred">Validity: <span style="color: black;">{{ deal.date_to }}</span></p>
                                                        <div class="" style="max-height: 22px;overflow: hidden;">
                                                            <v-icon small>mdi-map-marker</v-icon>
                                                            {{ deal.city }}, {{ deal.state }}
                                                        </div>
                                                    </div>
                                                    <div class="mcontact">
                                                        <a :href="('https://wa.me/'+deal.mobile_phone+'?text=Enquiry from Bizlx : '+currentURL)" target="_blank">
                                                            <v-btn fab x-small color="success">
                                                                <v-icon>mdi-whatsapp</v-icon>
                                                            </v-btn>
                                                        </a>
                                                        <a :href="('tel:'+ deal.mobile_phone)" target="_blank">
                                                            <v-btn fab x-small color="primary" class="my-2">
                                                                <v-icon>mdi-phone</v-icon>
                                                            </v-btn>
                                                        </a>
                                                        <a :href="'/deals/detail/'+deal.hotdeal_slug">
                                                            <v-btn fab x-small color="info">
                                                                <v-icon>mdi-eye-outline</v-icon></v-btn>
                                                        </a>
                                                    </div>
                                                </div>
                                            </v-card>
                                        </a>
                                    </carousel>
                                </div>
                                <div v-else class="text-center">
                                    <div>No Hot Deals in {{cats.subcat_name}}</div>
                                </div>
                            </v-card-text>
                        </v-card>
                    </b-card-text>
                </b-tab>
                <b-tab title="Sales">
                    <b-card-text>
                        <v-card flat>
                            <v-card-text class="p-0">
                                <div v-if="cats.sales.length > 0">
                                    <carousel :autoplay="false" :margin="30" :dots="false" :navText="['‹', '›']" :slideBy="1"
                                              :responsive = "{0:{items:1},576:{items:2},768:{items:2.3},992:{items:3},1200:{items:4}}" class="hotdels">
                                        <a v-for="(sale, i) in cats.sales" :key="i" class="item link-dark"
                                                     :href="'/deals/detail/'+sale.sale_slug">
                                            <v-card>
                                                <a :href="'/deals/detail/'+sale.sale_slug">
                                                    <div v-if="sale.sale_imageurl != null">
                                                        <v-img contain :src=(url+sale.sale_imageurl) height="150"></v-img>
                                                    </div>
                                                    <div v-else>
                                                        <v-img :src="('https://dummyimage.com/299x150/969096/3f4042.jpg&text='+sale.first_last_letter)" cover height="150"></v-img>
                                                    </div>
                                                </a>
                                                <div class="mt-2 p-2 d-flex">
                                                    <div class="lt w-100">
                                                        <div class="mb-1 fw-bold">
                                                            <a :href="'/deals/detail/'+sale.sale_slug" class="text-decoration-none link-dark h5">
                                                                {{ sale.sale_title }}
                                                            </a>
                                                        </div>
                                                        <!-- <div v-if="!loggedUser" class="fw-bold">Add To Wishlist
                                                          <v-icon>mdi-heart-outline</v-icon>
                                                        </div>
                                                        <div v-else class="fw-bold">Add To Wishlist
                                                          <v-icon v-if="sale.user_added_wishlist!=null" color="red" small>mdi-heart</v-icon>
                                                          <v-icon v-else small>mdi-heart-outline</v-icon>
                                                        </div> -->
                                                        <div class="cred">Sale
                                                            <del class="ms-1 h6" style="color: black;">₹{{ sale.normal_price }}</del>
                                                            <span class="ms-1 h4" style="color: black;">₹{{ sale.sale_price }}</span>
                                                        </div>
                                                        <div class="cred">Validity: <span style="color: black;">{{ sale.saledate_from }} - {{ sale.saledate_to }} </span></div>
                                                        <div class="address">
                                                            <v-icon small>mdi-map-marker</v-icon>
                                                            {{ sale.city }}, {{ sale.state }}
                                                            <a :href="'/deals/detail/'+sale.sale_slug" class="cred">Read More</a>
                                                        </div>
                                                    </div>
                                                    <div class="mcontact">
                                                        <a :href="('https://wa.me/'+sale.mobile_phone+'?text=Enquiry from Bizlx : '+currentURL)" target="_blank">
                                                            <v-btn fab x-small color="success">
                                                                <v-icon>mdi-whatsapp</v-icon>
                                                            </v-btn>
                                                        </a>
                                                        <a :href="('tel:'+ sale.mobile_phone)" target="_blank">
                                                            <v-btn fab x-small color="primary" class="my-2">
                                                                <v-icon>mdi-phone</v-icon>
                                                            </v-btn>
                                                        </a>
                                                        <a :href="'/deals/detail/'+sale.sale_slug">
                                                            <v-btn fab x-small color="info">
                                                                <v-icon>mdi-eye-outline</v-icon></v-btn>
                                                        </a>
                                                    </div>
                                                </div>
                                            </v-card>
                                        </a>
                                    </carousel>
                                </div>
                                <div v-else class="text-center">
                                    <div>No Sales in {{cats.subcat_name}}</div>
                                </div>
                            </v-card-text>
                        </v-card>
                    </b-card-text>
                </b-tab>
                <b-tab title="Jobs">
                    <b-card-text>
                        <v-card flat>
                            <v-card-text class="p-0">
                                <div v-if="cats.job.length > 0">
                                    <carousel :autoplay="false" :margin="10" :dots="false" :navText="['‹', '›']" :slideBy="1"
                                              :responsive = "{0:{items:1},576:{items:2},768:{items:2.3},992:{items:3},1200:{items:4}}"  class="vsearch">
                                        <a v-for="(job, i) in cats.job" :key="i" class="item link-dark"
                                                     :href="'/deals/detail/'+job.job_slug">
                                            <v-card>
                                                <a :href="'/deals/detail/'+job.job_slug" class="link-dark text-decoration-none">
                                                    <v-img :src="url+job.mainimage" alt="Job Image" height="150"></v-img>
                                                </a>
                                                <div class="p-2 mt-2 d-flex">
                                                    <div class="lt w-100">
                                                        <p class="mb-1 fw-bold">
                                                            <a :href="'/deals/detail/'+job.job_slug" class="link-dark text-decoration-none">
                                                                {{ job.job_title }}
                                                            </a></p>
                                                        <p class="mb-0">
                                                            <a to="/jobs/detail" class="text-decoration-none cblu">
                                                                {{ job.name }}
                                                            </a>
                                                        </p>
                                                        <div v-if="job.min_exp > 0">
                                                            <div class="fs11">Experience: {{ job.min_exp }} Years</div>
                                                        </div>
                                                        <div v-else>
                                                            <div class="fs11">Experience: Fresher</div>
                                                        </div>
                                                        <p class="mb-0">
                                                          <span class="fw-bold">
                                                             ₹{{ job.salary_from }}  a Month
                                                          </span>
                                                        </p>
                                                        <div class="">
                                                            <v-icon small>mdi-map-marker</v-icon>
                                                            {{ job.city }}, {{ job.state }}
                                                        </div>
                                                        <a :href="'/deals/detail/'+job.job_slug" class="text-decoration-none cred">Read More</a>
                                                    </div>
                                                    <div class="mcontact">
                                                        <a :href="('https://wa.me/'+job.mobile_phone+'?text=Enquiry from Bizlx : '+currentURL)" target="_blank">
                                                            <v-btn fab x-small color="success">
                                                                <v-icon>mdi-whatsapp</v-icon>
                                                            </v-btn>
                                                        </a>
                                                        <a :href="('tel:'+ job.mobile_phone)" target="_blank">
                                                            <v-btn fab x-small color="primary" class="my-2">
                                                                <v-icon>mdi-phone</v-icon>
                                                            </v-btn>
                                                        </a>
                                                        <a :href="'/deals/detail/'+job.job_slug">
                                                            <v-btn fab x-small color="info">
                                                                <v-icon>mdi-eye-outline</v-icon></v-btn>
                                                        </a>
                                                    </div>
                                                </div>
                                            </v-card>
                                        </a>
                                    </carousel>
                                </div>
                                <div v-else class="text-center">
                                    <div>No Jobs in {{cats.subcat_name}}</div>
                                </div>
                            </v-card-text>
                        </v-card>
                    </b-card-text>
                </b-tab>
                <b-tab title="Video">
                    <b-card-text>
                        <v-card flat>
                            <v-card-text class="p-0">
                                <div v-if="cats.video.length > 0">
                                    <carousel :autoplay="false" :margin="10" :dots="false" :navText="['‹', '›']" :slideBy="1"
                                              :responsive = "{0:{items:1},576:{items:2},768:{items:2.3},992:{items:3},1200:{items:4}}"  class="vsearch">
                                        <div v-for="(video, i) in cats.video" :key="i" class="item">
                                            <div class="item">
                                                <div class="ratio ratio-16x9">
                                                    <iframe :src="('https://www.youtube.com/embed/'+video.youtube_id)" :title="video.video_title" allowfullscreen></iframe>
                                                </div>
                                            </div>
                                        </div>
                                    </carousel>
                                </div>
                                <div v-else class="text-center">
                                    <div>No Videos in {{cats.subcat_name}}</div>
                                </div>
                            </v-card-text>
                        </v-card>
                    </b-card-text>
                </b-tab>
            </b-tabs>
        </v-row>
        <div class="position-relative mobile" v-for="cats in catslider" :key="cats.id">
            <div class="mb-4"><span class="cblu bbred h5">{{ cats.subcat_name }}</span> </div>
            <b-tabs pills active-nav-item-class="fw-bold bgred mb-2 text-white"  nav-class="fw-bold mtabs">
                <b-tab title="Info" active >
                    <b-card-text>
                        <v-card flat>
                            <div v-if="cats.info.length > 0">
                                <carousel :autoplay="false" :margin="5" :items="4" :dots="false" :slideBy="1" :navText="['‹','›']"
                                          :responsive = "{0:{items:1},576:{items:2},768:{items:3},1200:{items:4}}" class="hotels mobile">
                                    <a v-for="(hotel,i) in cats.info" :key="i"
                                                 class="item mitem d-flex link-dark"
                                                 :href="'/'+hotel.business_uname">
                                        <a :href="'/'+hotel.business_uname" class="link-dark text-decoration-none">
                                            <div class="mimg" v-if="hotel.mainimage != null">
                                                <v-img cover class="rounded" :src=(url+hotel.mainimage) max-width="110" height="110"></v-img>
                                            </div>
                                            <div v-else>
                                                <v-img cover class="rounded" max-width="110" height="110" :src="('https://dummyimage.com/299x150/969096/3f4042.jpg&text='+hotel.first_last_letter)"></v-img>
                                            </div>
                                        </a>
                                        <div class="mdetails">
                                            <a :href="'/'+hotel.business_uname" class="link-dark text-decoration-none">
                                                <div class="mtitle">{{ hotel.name }}</div>
                                            </a>
                                            <div class="maddress">
                                                <v-icon small>mdi-map-marker</v-icon>
                                                {{hotel.city}}, {{hotel.state}}
                                            </div>
                                            <div class="mreviews d-flex align-items-center">
                                                <v-rating dense color="warning" size="12" length="5" :value=hotel.rating readonly></v-rating><span class="badge bg-dark ms-2">{{ hotel.rating }}</span>
                                            </div>
                                        </div>
                                        <div class="mcontact">
                                            <a :href="('https://wa.me/'+hotel.mobile_phone+'?text=Enquiry from Bizlx : '+currentURL)" target="_blank">
                                                <v-btn fab x-small color="success">
                                                    <v-icon>mdi-whatsapp</v-icon>
                                                </v-btn>
                                            </a>
                                            <a :href="('tel:'+ hotel.mobile_phone)" target="_blank">
                                                <v-btn fab x-small color="primary" class="my-2">
                                                    <v-icon>mdi-phone</v-icon>
                                                </v-btn>
                                            </a>
                                            <a :href="'/'+hotel.business_uname">
                                                <v-btn fab x-small color="info">
                                                    <v-icon>mdi-eye-outline</v-icon></v-btn>
                                            </a>
                                        </div>
                                    </a>
                                </carousel>
                            </div>
                            <div v-else class="text-center">
                                <div>No Business in {{cats.subcat_name}}</div>
                            </div>
                        </v-card>
                    </b-card-text>
                </b-tab>
                <b-tab title="Hot Deals" >
                    <b-card-text>
                        <v-card flat>
                            <div v-if="cats.hotdeal.length > 0">
                                <carousel :autoplay="false" :margin="5" :dots="false" :navText="['‹', '›']" :slideBy="1"
                                          :responsive = "{ 0: { items: 1 }, 576: { items: 2 }, 768: { items: 3 }, 1200: { items: 4 } }" class="hotdels mobile">
                                    <a v-for="(deal, i) in cats.hotdeal" :key="i" class="item mitem d-flex link-dark" :href="'/deals/detail/'+deal.hotdeal_slug">
                                        <div class="mimg" v-if="deal.hotdeal_image_url != ''">
                                            <a :href="'/deals/detail/'+deal.hotdeal_slug" class="link-dark text-decoration-none">
                                                <v-img cover class="rounded" :src=(url+deal.hotdeal_image_url) max-width="110" height="110"></v-img>
                                            </a>
                                        </div>
                                        <div v-else>
                                            <a :href="'/deals/detail/'+deal.hotdeal_slug" class="link-dark text-decoration-none">
                                                <v-img cover class="rounded" max-width="110" height="110" :src="('https://dummyimage.com/299x150/969096/3f4042.jpg&text='+deal.first_last_letter)"></v-img>
                                            </a>
                                        </div>
                                        <div class="mdetails">
                                            <a :href="'/deals/detail/'+deal.hotdeal_slug" class="link-dark text-decoration-none mtitle fs12">
                                                {{ deal.hot_deal_title }}
                                            </a>
                                            <div class="fs12">
                                                By: <a :href="'/'+deal.business_uname">
                                                {{ deal.name }}
                                            </a>
                                            </div>
                                            <p class="mb-0 cred fs12">Price:
                                                <span style="color: black;">
                                   ₹{{ deal.price_to }}
                                </span>
                                            </p>
                                            <p class="mb-0 cred fs12">Validity: <span style="color: black;">{{ deal.date_to }}</span></p>
                                            <div class="fs12">
                                                <v-icon small>mdi-map-marker</v-icon>
                                                {{ deal.city }}, {{ deal.state }}
                                            </div>
                                        </div>
                                        <div class="mcontact fs12">
                                            <a :href="('https://wa.me/'+deal.mobile_phone+'?text=Enquiry from Bizlx : '+currentURL)" target="_blank">
                                                <v-btn fab x-small color="success">
                                                    <v-icon>mdi-whatsapp</v-icon>
                                                </v-btn>
                                            </a>
                                            <a :href="('tel:'+ deal.mobile_phone)" target="_blank">
                                                <v-btn fab x-small color="primary" class="my-2">
                                                    <v-icon>mdi-phone</v-icon>
                                                </v-btn>
                                            </a>
                                            <a :href="'/deals/detail/'+deal.hotdeal_slug">
                                                <v-btn fab x-small color="info">
                                                    <v-icon>mdi-eye-outline</v-icon></v-btn>
                                            </a>
                                        </div>
                                    </a>
                                </carousel>
                            </div>
                            <div v-else class="text-center">
                                <div>No Hot Deals in {{cats.subcat_name}}</div>
                            </div>
                        </v-card>
                    </b-card-text>
                </b-tab>
                <b-tab title="Sales">
                    <b-card-text>
                        <v-card flat>
                            <v-card-text class="p-0">
                                <div v-if="cats.sales.length > 0">
                                    <carousel :autoplay="false" :margin="5" :dots="false" :navText="['‹', '›']" :slideBy="1"
                                              :responsive = "{ 0: { items: 1 }, 576: { items: 2 }, 768: { items: 3 }, 1200: { items: 4 } }" class="hotdels">
                                        <a v-for="(sale, i) in cats.sales" :key="i" class="item mitem d-flex link-dark" :href="'/sales/detail/'+sale.sale_slug">
                                            <v-card>
                                                <a :href="'/sales/detail/'+sale.sale_slug">
                                                    <div v-if="sale.sale_imageurl != null">
                                                        <v-img cover class="rounded" :src=(url+sale.sale_imageurl) max-width="110" height="110"></v-img>
                                                    </div>
                                                    <div v-else>
                                                        <v-img cover class="rounded" :src="('https://dummyimage.com/400x350/fd0606/fff&text='+sale.first_last_letter)" max-width="110" height="110"></v-img>
                                                    </div>
                                                </a>
                                                <!-- <div class="mt-2 p-2 d-flex"> -->
                                                <div class="mdetails">
                                                    <a :href="'/sales/detail/'+sale.sale_slug" class="text-decoration-none link-dark mtitle fs12">
                                                        {{ sale.sale_title }}
                                                    </a>
                                                    <div class="fs12">
                                                        By: <a :href="'/'+sale.business_uname">
                                                        {{ sale.name }}
                                                    </a>
                                                    </div>
                                                    <div class="cred fs12">Sale
                                                        <del class="ms-1 h6" style="color: black;">₹{{ sale.normal_price }}</del>
                                                        <span class="ms-1 h4" style="color: black;">₹{{ sale.sale_price }}</span>
                                                    </div>
                                                    <div class="cred fs12">Validity: <span style="color: black;">{{ sale.saledate_from }} - {{ sale.saledate_to }} </span></div>
                                                    <div class="address fs12">
                                                        <v-icon small>mdi-map-marker</v-icon>
                                                        {{ sale.city }}, {{ sale.state }}
                                                        <a :href="'/sales/detail/'+sale.sale_slug" class="cred">Read More</a>
                                                    </div>
                                                </div>
                                                <div class="mcontact fs12">
                                                    <a :href="('https://wa.me/'+sale.mobile_phone+'?text=Enquiry from Bizlx : '+currentURL)" target="_blank">
                                                        <v-btn fab x-small color="success">
                                                            <v-icon>mdi-whatsapp</v-icon>
                                                        </v-btn>
                                                    </a>
                                                    <a :href="('tel:'+ sale.mobile_phone)" target="_blank">
                                                        <v-btn fab x-small color="primary" class="my-2">
                                                            <v-icon>mdi-phone</v-icon>
                                                        </v-btn>
                                                    </a>
                                                    <a :href="'/sales/detail/'+sale.sale_slug">
                                                        <v-btn fab x-small color="info">
                                                            <v-icon>mdi-eye-outline</v-icon></v-btn>
                                                    </a>
                                                </div>
                                                <!-- </div> -->
                                            </v-card>
                                        </a>
                                    </carousel>
                                </div>
                                <div v-else class="text-center">
                                    <div class="fs14">No Sales in {{cats.subcat_name}}</div>
                                </div>
                            </v-card-text>
                        </v-card>
                    </b-card-text>
                </b-tab>
                <b-tab title="Jobs" >
                    <b-card-text>
                        <v-card flat>
                            <div v-if="cats.job.length > 0">
                                <carousel :autoplay="false" :margin="5" :dots="false" :navText="['‹', '›']" :slideBy="1"
                                          :responsive = "{ 0: { items: 1 }, 576: { items: 2 }, 768: { items: 3 }, 1200: { items: 4 } }"  class="vsearch">
                                    <a v-for="(job, i) in cats.job" :key="i" class="item mitem d-flex link-dark"
                                                 :href="'/jobss/detail/'+job.job_slug">
                                        <!-- <v-card> -->
                                        <div class="mimg" v-if="job.mainimage != null">
                                            <a :href="'/jobs/detail/'+job.job_slug" class="link-dark text-decoration-none">
                                                <v-img :src="url+job.mainimage" cover class="rounded"  max-width="110" alt="Job Image" height="110"></v-img>
                                            </a>
                                        </div>
                                        <div v-else>
                                            <a :href="'/jobs/detail/'+job.job_slug" class="link-dark text-decoration-none">
                                                <v-img :src="url+'/images/placeholder-job.svg'" cover class="rounded"  max-width="110" alt="Job Image" height="110"></v-img>
                                            </a>
                                        </div>
                                        <div class="mdetails">
                                            <a to="/jobs/detail" class="link-dark text-decoration-none mtitle fs12">
                                                {{ job.job_title }}
                                            </a>
                                            <p class="mb-0 fs12">
                                                <a :href="'/jobss/detail/'+job.job_slug" class="text-decoration-none cblu">
                                                    {{ job.name }}
                                                </a>
                                            </p>
                                            <div class="fs12" v-if="job.min_exp > 0">
                                                <div class="fs11">Experience: {{ job.min_exp }} Years</div>
                                            </div>
                                            <div class="fs12" v-else>
                                                <div class="fs11">Experience: Fresher</div>
                                            </div>
                                            <p class="mb-0 fs12">
                                  <span class="fw-bold">
                                    {{ job.salary_from }}  a Month
                                  </span>
                                            </p>
                                            <div class="fs12">
                                                <v-icon small>mdi-map-marker</v-icon>
                                                {{ job.city }}, {{ job.state }}
                                            </div>
                                            <a :href="'/jobss/detail/'+job.job_slug" class="text-decoration-none cred fs12">Read More</a>
                                        </div>

                                        <div class="mcontact fs12">
                                            <a :href="('https://wa.me/'+job.mobile_phone+'?text=Enquiry from Bizlx : '+currentURL)" target="_blank">
                                                <v-btn fab x-small color="success">
                                                    <v-icon>mdi-whatsapp</v-icon>
                                                </v-btn>
                                            </a>
                                            <a :href="('tel:'+ job.mobile_phone)" target="_blank">
                                                <v-btn fab x-small color="primary" class="my-2">
                                                    <v-icon>mdi-phone</v-icon>
                                                </v-btn>
                                            </a>
                                            <a :href="'/jobss/detail/'+job.job_slug">
                                                <v-btn fab x-small color="info">
                                                    <v-icon>mdi-eye-outline</v-icon></v-btn>
                                            </a>
                                        </div>
                                        <!-- </v-card> -->
                                    </a>
                                </carousel>
                            </div>
                            <div v-else class="text-center">
                                <div>No Jobs in {{cats.subcat_name}}</div>
                            </div>
                        </v-card>
                    </b-card-text>
                </b-tab>
                <b-tab title="Video" >
                    <b-card-text>
                        <v-card flat>
                            <div v-if="cats.video.length > 0">
                                <carousel :autoplay="false" :margin="10" :dots="false" :navText="['‹', '›']" :slideBy="1"
                                          :responsive = "{ 0: { items: 1 }, 576: { items: 2 }, 768: { items: 3 }, 1200: { items: 4 } }"  class="vsearch">
                                    <div v-for="(video, i) in cats.video" :key="i" class="item">
                                        <div class="item">
                                            <div class="ratio ratio-16x9">
                                                <iframe :src="('https://www.youtube.com/embed/'+video.youtube_id)" :title="video.video_title" allowfullscreen></iframe>
                                            </div>
                                        </div>
                                    </div>
                                </carousel>
                            </div>
                            <div v-else class="text-center">
                                <div>No Videos in {{cats.subcat_name}}</div>
                            </div>
                        </v-card>
                    </b-card-text>
                </b-tab>
            </b-tabs>
        </div>
    </v-container>
</template>

<script>
import carousel from 'vue-owl-carousel'
export default {
    name: "HomePage",
    components:{carousel},
    data(){
        return {
            url: 'https://bizlx.s3.ap-south-1.amazonaws.com',
            loading:true,
            dialog:false,
            itemsPerRow:2,
            itemsPerRow3:3,
            subcats:[],
            ads:[],
            jobs:[],
            slides:[],
            catslider: [],
            loggedUserId:1,
            currentURL:''
        }
    },
    created() {
        this.getSubcategory();
        this.allTopads();
        this.getJobcategory();
        this.allCitydeals();
        this.getSlider();
        this.currentURL = window.location.href
    },
    methods:{
        async getSubcategory(){
            await axios.get('/api/populars')
                .then((resp)=>{
                    this.subcats = resp.data.populars;
                });
        },
        sendSearch(catId,catName,popup){
            console.log('sending popup flag:', popup);
            localStorage.setItem('popup', popup);
            let subcat_id = catId;
            let subcat_name = catName;
            let dealtype = 3;

            var catSearchedValues =  {
                selectSubCategoty:catId,
                selectSubCatname:catName,
                selectCity:'',
                selectCityname:'',
                jobcat_name:'',
                deal:'',
            }
            this.$store.dispatch('setsubcatid', {subcat_id})
            this.$store.dispatch('setdealtypeid',{dealtype});
            localStorage.setItem('dealtype',dealtype);
            localStorage.setItem('city_id',null);
            localStorage.setItem('popup',popup);
            localStorage.setItem("catSearchedValues", JSON.stringify(catSearchedValues));
            window.location.href = '/searchall';
        },
       
//         sendSearch(catId, catName, popup) {
//   console.log('sending popup flag:', popup);

//   // 1️⃣ Remove *all* previous filter state:
//   localStorage.removeItem('catSearchedValues');
//   localStorage.removeItem('subcat_id');
//   localStorage.removeItem('dealtype');
//   localStorage.removeItem('city_id');
//   localStorage.removeItem('popup');

//   // 2️⃣ Build a minimal query-string with just what you need:
//   const qs = new URLSearchParams({
//     dealtype:  3,
//     subcat_id: catId,
//     popup:     popup ? '1' : '0'
//   }).toString();

//   // 3️⃣ Navigate:
//   window.location.href = `/searchall?${qs}`;
// },
        async allTopads(){
            await axios.get('/api/home/topads')
                .then((resp)=>{
                    this.ads = resp.data.topads;
                });
        },
        async getJobcategory() {
            await axios.get("/api/jobcategory")
                .then((resp) =>{
                    this.jobs =  resp.data.jobcategories;
                });
        },
        async allCitydeals(){
            await axios.get('/api/home/citydeal')
                .then((resp)=>{
                    this.slides = resp.data.citydeals;
                    this.loading = false;
                });
        },
        showListing(cityId,cityName){
            let city_id = cityId;
            let city = cityName;
            let dealtype = 3;
            let setsubcatid ="";
            var catSearchedValues =  {
                selectSubCategoty:'',
                selectSubCatname:'',
                selectCity:cityId,
                selectCityname:cityName,
                jobcat_name:'',
                deal:'',
            }
            this.$store.dispatch('setcityid',{city_id});
            this.$store.dispatch('setdealtypeid',{dealtype});
            this.$store.dispatch('setsubcatid',setsubcatid);
            localStorage.setItem('city_id',cityId);
            localStorage.setItem('dealtype',dealtype);
            localStorage.setItem('subcat_id',null);
            localStorage.setItem("catSearchedValues", JSON.stringify(catSearchedValues));
            window.location.href = '/searchall';
        },
        showDeals(cityId,cityName){
            let city_id = cityId;
            let dealtype = 0;
            let setsubcatid ="";
            var catSearchedValues =  {
                selectSubCategoty:'',
                selectSubCatname:'',
                selectCity:cityId,
                selectCityname:cityName,
                jobcat_name:'',
                deal:'',
            }
            this.$store.dispatch('setcityid',{city_id});
            this.$store.dispatch('setdealtypeid',{dealtype});
            this.$store.dispatch('setsubcatid',setsubcatid);
            localStorage.setItem('city_id',city_id);
            localStorage.setItem('dealtype',dealtype);
            localStorage.setItem('subcat_id',null);
            localStorage.setItem("catSearchedValues", JSON.stringify(catSearchedValues));
            window.location.href = '/searchall?dealtype='+dealtype;
        },
        async getSlider() {
            await axios.get('/api/home/catsliders'+'/'+this.loggedUserId)
                .then((resp) => {
                    this.catslider = resp.data.catsliders;
                });
        },
    },
    computed: {
        catGroups () {
            return Array.from(Array(Math.ceil(this.subcats.length / this.itemsPerRow)).keys())
        },
        subcatGroups () {
            return Array.from(Array(Math.ceil(this.subcats.length / this.itemsPerRow3)).keys())
        },
    },
}
</script>

<style scoped>
div.cats img {
    width: auto;
    margin: 0 auto;
    }
div.cats p.category {
    text-align: center;
    height: 42px;
    overflow: hidden;
    margin-bottom: 0;
    }
div.adtitle{
    font-weight: 700;
    font-size: 20px;
    max-height: 30px;
    overflow: hidden;
    }
div.loc {
    font-size: 12px;
    }
.find-job img.img-fluid {
    width: 64px;
    margin: 0 auto;
    }
div.find-job p.job-category {
    text-align: center;
    height: 42px;
    overflow: hidden;
    margin-bottom: 0;
    }
.listings {top: 5px;left: 5px;}
.deals {top: 5px;right: 5px;}
.place {bottom: 0;width: 100%;}
.v-image__image.v-image__image--cover {
    background-color: rgba(0,0,0,0.5);
    background-blend-mode: overlay;
    }
@media (max-width: 576px){
    div.cats p.category {
        text-align: center;
        height: 36px;
        overflow: hidden;
        font-size: 12px;
        }
    .desktop{display: none;}
    .find-job img.img-fluid {
        width: 32px;
        margin: 0 auto;
        }
    div.find-job p.job-category {
        text-align: center;
        height: 36px;
        overflow: hidden;
        font-size: 12px;
        }
    div.adtitle{
        font-weight: 700;
        font-size: 14px;
        max-height: 21px;
        overflow: hidden;
        }
    div.loc {
        font-size: 11px;
        }
    }

@media (min-width: 576.1px) {
    .mobile{display: none;}
    }

.listings {top: 5px;left: 5px;}
.deals {top: 5px;right: 5px;}
.place {bottom: 0;width: 100%;}
.v-image__image.v-image__image--cover {
    background-color: rgba(0,0,0,0.5);
    background-blend-mode: overlay;
    }
@media (max-width: 576px){
    .city .v-btn.v-size--small {
        font-size: 0.60rem;
        text-transform: capitalize;
        padding: 0 6px;
        }
    }
.item.dd {
    cursor: pointer;
    }

.mdetails {
    padding: 7px 0 0 7px;
    width:100%
    }
a.mitem{text-decoration: none;}
.mtitle {
    font-size: 14px;
    font-weight: 600;
    }
.fs12{font-size: 12px;}
.item.mitem {
    box-shadow: 0 6px 10px -6px rgba(0,0,0,.5);
    margin-bottom: 15px;
    background: #f1f2f380;
    padding: 7px;
    }
.desktop a, .mobile a {text-decoration: none;}
</style>
