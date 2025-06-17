<template>
    <div class="container">
        <div class="row">
            <!--            <div v-if="dealtype_selected === 3" class="row">-->
            <v-col cols="12">
                <!-- <div class="all">
                    <carousel v-if="features.length > 0 && carouselKey" :key="carouselKey" :autoplay="false" :margin="10" :items="4" :dots="false" :slideBy="1"
                              :navText="['‹','›']" :responsive = "{0:{items:2},576:{items:2},768:{items:3},992:{items:3},1200:{items:4}}" class="deals">
                        <div v-for="feature in features" :key="feature.user_id" class="item">
                            <a :href="'/'+feature.username" class="link-dark text-decoration-none">
                                <v-card>
                                    <v-img v-if="feature.galleryimage != null" cover :src=url+feature.galleryimage.image_url height="150"
                                           class="white--text align-end" gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.5)">
                                        <div class="p-1 fethead">
                                            <div class="adtitle">{{ feature.name }}</div>
                                            <div class="loc">
                                                {{ feature.cat_name }}
                                            </div>
                                        </div>
                                    </v-img>
                                    <v-img v-else cover :src=(picsumd+feature.user_id) height="150"  class="white--text align-end" gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.5)">
                                        <div class="p-1 fethead">
                                            <div class="adtitle">{{ feature.name }}</div>
                                            <div class="loc">
                                                {{ feature.cat_name }}
                                            </div>
                                        </div>
                                    </v-img>
                                    <div class="feat badge bg-yell text-black">Featured</div>
                                </v-card>
                            </a>
                        </div>
                    </carousel>
                </div> -->
                <carousel
    v-if="features.length"
    :key="carouselKey"
    :autoplay="false"
    :margin="10"
    :items="4"
    :dots="false"
    :slideBy="1"
    :navText="['‹','›']"
    :responsive="{0:{items:2},576:{items:2},768:{items:3},992:{items:3},1200:{items:4}}"
    class="deals"
    >
    <div v-for="feature in features" :key="feature.user_id" class="item">
        <a :href="`/${feature.username}`" class="link-dark text-decoration-none">
        <v-card>
            <!-- real gallery -->
            <v-img
            v-if="feature.galleryimage"
            cover
            :src="url + feature.galleryimage.image_url"
            height="150"
            class="white--text align-end"
            gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.5)"
            >
            <div class="p-1 fethead">
                <div class="adtitle">{{ feature.name }}</div>
                <div class="loc">{{ feature.cat_name }}</div>
            </div>
            </v-img>

            <!-- fallback placeholder with identical styling -->
            <v-img
            v-else
            cover
            :src="`https://placehold.co/400x400?text=${encodeURIComponent(feature.name)}`"
            height="150"
            class="white--text align-end"
            gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.5)"
            >
            <div class="p-1 fethead">
                <div class="adtitle">{{ feature.name }}</div>
                <div class="loc">{{ feature.cat_name }}</div>
            </div>
            </v-img>

            <div class="feat badge bg-yell text-black">Featured</div>
        </v-card>
        </a>
    </div>
    </carousel>
            
            </v-col>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="small">
                    <h4 class="cblu bbred">
                        <span v-for="dtype in dealtype" :key="dtype.id">
                            <span v-if="dtype.id == dealtype_selected">
                                <span v-for="scat in subcats" :key="scat.id">
                                    <span v-if="form1.subcat_id == scat.id">{{scat.subcat_name}} </span>
                                </span>
                                <span v-for="jcat in jobs" :key="jcat.id">
                                    <span v-if="form1.jobcat_id == jcat.id">{{ jcat.job_cat_name }}</span>
                                </span>
                                <span v-if="dtype.id === 3 || dtype.id === 4" class="ms-1">Listing</span>
                                <span v-else>{{dtype.name}}</span>
                            </span>
                        </span>
                        <span v-for="loc in locs" :key="loc.city_id">
                            <span v-if="loc.city_id == form1.city_id"><span class="mx-1">in</span>{{loc.city}}</span>
                        </span>
                    </h4>
                </div>
            </div>
            <div class="ipad mb-4">
                <div class="d-flex justify-content-center">
                    <v-btn small @click="onMobile" color="info" class="shadow_cls">
                        <v-icon v-if="dealtype_selected === 2" color="white" small class="me-1">mdi-eye</v-icon>
                        <v-icon v-else color="white" small class="me-1">mdi-radio-tower</v-icon>
                        <span v-if="dealtype_selected === 2">More Jobs</span>
                        <span v-else>Deals Radar</span>
                    </v-btn>
                </div>
                <!-- <div class="d-flex justify-content-center">
                    <v-btn
                        v-if="dealtype_selected === 2 || dealtype_selected === 0 || dealtype_selected === 1"
                        small
                        @click="onMobile"
                        color="info"
                        class="shadow_cls"
                    >
                        <v-icon v-if="dealtype_selected === 2" color="white" small class="me-1">mdi-eye</v-icon>
                        <v-icon v-else color="white" small class="me-1">mdi-radio-tower</v-icon>
                        <span v-if="dealtype_selected === 2">More Jobs</span>
                        <span v-else>Deals Radar</span>
                    </v-btn>
                </div> -->
                <div class="small">
                    <div>
                        <span v-if="form1.searchy" class="h6">Search Result:</span>
                        <span class="ms-1 h6">{{form1.searchy}}</span>
                    </div>
                    
                    <div v-if="form1.subcat_id">
                        <span v-for="scat in subcats" :key="scat.id">
                            <span v-if="form1.subcat_id == scat.id" class="h6">Category: {{scat.subcat_name}} </span>
                        </span>
                    </div>
                    <div v-if="form1.jobcat_id">
                        <span v-for="jcat in jobs" :key="jcat.id">
                            <span v-if="form1.jobcat_id == jcat.id" class="h6">Job Type: {{jcat.job_cat_name}} </span>
                        </span>
                    </div>
                    <div v-if="form1.city_id">
                        <span v-for="loc in locs" :key="loc.city_id">
                            <span v-if="loc.city_id == form1.city_id" class="h6">Location: {{loc.city}}</span>
                        </span>
                    </div>
                </div>
                <div>
                    <div v-if="form1.min_price && form1.max_price === '' " class="h6 mb-0">
                        <span> Price: {{form1.min_price}} to {{ maxprice }}</span>
                    </div>
                    <div v-else-if="form1.min_price" class="h6 mb-0">
                        Price: {{form1.min_price}}
                        <!--            <span v-if="form1.max_price > form1.min_price">to {{form1.max_price}} d2</span>-->
                        <span>to {{form1.max_price}}</span>
                    </div>
                    <div class="h6 mb-0" v-if="form1.min_price === '' && form1.max_price > 0">Price : 0
                        <span>to {{form1.max_price}}</span>
                    </div>
                    <div v-if="form1.radius1 > 0" class="h6 mb-0">Within: {{form1.radius1}} KM</div>
                </div>
            </div>
            <div class="col-12 col-md-12 col-lg-4 col-xxl-3 position-relative pt-0 m992">
                <div class="small">
                    <div>
                        <span v-if="form1.searchy" class="h6">Search Result:</span>
                        <span class="ms-1 h6">{{form1.searchy}}</span>
                    </div>
                    <div v-if="form1.subcat_id">
                        <span v-for="scat in subcats" :key="scat.id">
                            <span v-if="form1.subcat_id == scat.id" class="h6">Category: {{scat.subcat_name}} </span>
                        </span>
                    </div>
                    <div v-if="form1.jobcat_id">
                        <span v-for="jcat in jobs" :key="jcat.id">
                            <span v-if="form1.jobcat_id == jcat.id" class="h6">Job Type: {{jcat.job_cat_name}} </span>
                        </span>
                    </div>
                    <div v-if="form1.city_id">
                        <span v-for="loc in locs" :key="loc.city_id">
                            <span v-if="loc.city_id == form1.city_id" class="h6">Location: {{loc.city}}</span>
                        </span>
                    </div>
                </div>
                <div>
                    <div v-if="form1.min_price && form1.max_price === '' " class="h6 mb-0">
                        <span> Price: {{form1.min_price}} to {{ maxprice }}</span>
                    </div>
                    <div v-else-if="form1.min_price" class="h6 mb-0">
                        Price: {{form1.min_price}}
                        <span>to {{form1.max_price}}</span>
                    </div>
                    <div class="h6 mb-0" v-if="form1.min_price === '' && form1.max_price > 0">Price : 0
                        <span>to {{form1.max_price}}</span>
                    </div>
                    <div v-if="form1.radius1 > 0" class="h6 mb-0">Within: {{form1.radius1}} KM</div>
                </div>
                <div class="mobile">
                    <div class="d-flex justify-content-center">
                        <v-btn small @click="onMobile" color="info" class="shadow_cls">
                            <v-icon v-if="dealtype_selected === 2" color="white" small class="me-1">mdi-eye</v-icon>
                            <v-icon v-else color="white" small class="me-1">mdi-radio-tower</v-icon>
                            <span v-if="dealtype_selected === 2">More Jobs</span>
                            <span v-else>Deals Radar</span>
                        </v-btn>
                    </div>
                </div>
                <!-- sidebar -->
                <div class="catside shades desktop ">
                    <div class="bg-white px-1 pt-2 pb-2">
                        <v-select v-model="form1.country_id" :items="countries" item-value="id" item-text="country"
                            hide-details dense @change="getLocs">
                        </v-select>
                        <v-autocomplete v-if="form1.dealtype_selected == 2" class="sel14 pt-4" v-model="form1.jobcat_id"
                            placeholder="Job Type" hide-details auto-select-first clearable  :items="jobs"
                            dense label="Job Type" item-text="job_cat_name" item-value="id"
                            @keyup.native="jobCategoryKeyUp">
                        </v-autocomplete>
                        <v-autocomplete
                            v-if="form1.dealtype_selected == 0 || form1.dealtype_selected == 1 || form1.dealtype_selected == 3 || form1.dealtype_selected == 4"
                            class="sel14 pt-4" v-model="form1.subcat_id" placeholder="Category" label="Category"
                            :items="subcats" dense item-text="subcat_name" ref="autocomplete" auto-select-first
                            clearable  hide-details item-value="id" @click.native="categoriesInputEvent"
                            @keyup.native="categoriesKeyUp" @change="onCatChange" @click:clear="onCatClear"
                            >
                        </v-autocomplete>
                        <v-autocomplete class="sel14 pt-4" v-model="form1.city_id" dense placeholder="Location"
                            hide-details auto-select-first clearable  :items="locs" label="Location"
                            item-text="city" item-value="city_id" @keyup.native="locationKeyUp"   @change="onCityChange"
                              @click:clear="onCityClear" >
                        </v-autocomplete>
                       
                        <!-- <v-autocomplete
                        clearable
                        class="sel14 pt-4"
                        v-model="form1.area"
                        :items="areas"
                        item-text="name"
                        item-value="name"           
                        :filter="() => true"
                        placeholder="Nearby Areas"
                        label="Nearby Areas"
                        :search-input.sync="areaSearch"
                        @update:search-input="areaKeyUp"
                        no-data-text="No nearby areas found"
                        /> -->
                        <v-autocomplete
                        clearable
                         class="sel14 pt-4"
                        v-model="form1.area"
                        :items="areas"
                        item-text="name"
                        item-value="name"
                         placeholder="Nearby Areas"
                        label="Nearby Areas"
                        :filter="() => true"
                        :loading="loadingAreas"                   
                        :search-input.sync="areaSearch"
                        @update:search-input="debouncedAreaSearch" 
                        no-data-text="No nearby areas found"
                         @change="onAreaChange"
 @click:clear="onAreaClear"
                        
                        />
                    </div>  
                   <!-- Desktop sidebar -->
<div
  id="dealtype"
  class="sec10 bg-white d-flex justify-content-between small pt-2 my-2"
>
  <div
    class="form-check"
    v-for="d1 in dealtype"
    :key="d1.id"
    :class="d1.bclas"
  >
    <input
      class="form-check-input"
      type="radio"
      :name="'dealtype'"
      :id="`dealtype-${d1.id}`"
      :value="d1.id"
      v-model="form1.dealtype_selected"
    >
    <label
      class="form-check-label"
      :for="`dealtype-${d1.id}`"
    >
      {{ d1.name }}
    </label>
  </div>
</div>


                    <div class="sec10 bg-white my-2">
                        <input type="text" class="form-control form-control-sm my-1" placeholder="Keyword Search"
                            v-model="form1.searchy">
                    </div>
                    <div v-if="form1.dealtype_selected === 2" class="h6 sec2">Salary Range</div>
                    <div v-if="form1.dealtype_selected === 1 || form1.dealtype_selected === 0" class="h6 sec2">Price
                        Range</div>
                    <div class="range-slider sec2 bg-white p-2"
                        v-if="form1.dealtype_selected === 2 || form1.dealtype_selected === 0|| form1.dealtype_selected === 1">
                        <span class="d-flex align-items-center">
                            <input type="number" v-model="form1.min_price"
                                class="form-control form-control-sm me-2 text-center" placeholder="From">
                            <input type="number" v-model="form1.max_price"
                                class="form-control form-control-sm ms-2 text-center" placeholder="To">
                        </span>
                    </div>
                    <div
                        v-if="form1.dealtype_selected === 0 || form1.dealtype_selected === 1 || form1.dealtype_selected === 3 || form1.dealtype_selected === 4 || form1.dealtype_selected === 5 ">
                        <div v-if="form1.city_id && form1.dealtype_selected === 0" class="h6 sec1">Deals Near By</div>
                        <div v-if="form1.city_id && form1.dealtype_selected === 1" class="h6 sec1">Sales Near By</div>
                        <div v-if="form1.city_id && form1.dealtype_selected === 3 || form1.city_id && form1.dealtype_selected === 4"
                            class="h6 sec1">List Near By</div>
                        <v-slider v-if="form1.city_id" v-model="form1.radius1" :thumb-color="ex3.color" :max="ex3.val"
                            thumb-label="always" label="KM" inverse-label>
                        </v-slider>
                        <div class=text-center>
                            <v-btn color="success" class="my-2 ms-10" @click="getNewdata(true)">Go</v-btn>
                            <v-btn color="primary" class="mt-2 float-end" @click="resetform1">Reset</v-btn>
                        </div>
                    </div>
                    <div v-if="form1.dealtype_selected === 2" class=text-center>
                        <v-btn color="success" class="my-2 ms-10" @click="getNewdata(true)">Go</v-btn>
                        <v-btn color="primary" class="mt-2 float-end" @click="resetform1">Reset</v-btn>
                    </div>
                </div>
                <!-- end sidebar -->
            </div>
            <!-- <div v-if="dealtype_selected === 0" class="col-12 col-md-12 col-lg-8 col-xxl-9 px-0 pt-0">
                <div v-if="hlist.length > 0">
                    <div class="desktop">
                        <v-card class="mb-7 small" v-for="hotd in hlist" :key="hotd.hotdeal_id">
                            <v-row align="center">
                                <v-col cols="4" class="py-0">
                                    <a :href="'/deals/detail/'+hotd.hotdeal_slug">
                                        <v-img v-if="hotd.hotdeal_single_image.hotdeal_img_url != null"
                                            :src="(url+hotd.hotdeal_single_image.hotdeal_img_url)" cover
                                            height="200"></v-img>
                                        <v-img v-if="hotd.hotdeal_single_image.hotdeal_img_url == null"
                                            :src="('https://placehold.co/600x400?text=' + hotd.hot_deal_title)" cover
                                            height="200">
                                        </v-img>
                                    </a>
                                </v-col>
                                <v-col cols="8" class="py-0">
                                    <v-row>
                                        <v-col md="8" cols="7" class="py-0">
                                            <a :href="'/deals/detail/'+hotd.hotdeal_slug"
                                                class="dtitle text-decoration-none link-dark h4">
                                                {{ hotd.hot_deal_title }}
                                            </a>
                                            <div><span class="me-1">Category: {{hotd.subcat_name}}</span>
                                            </div>
                                            <div class="cblu">By:
                                                <a :href="'/'+hotd.username" class="text-decoration-none">
                                                    {{ hotd.name }}
                                                </a>
                                            </div>
                                            <div class="cred">
                                                Price: <span style="color: black;">₹{{ hotd.price_to }}</span>
                                            </div>
                                            <div class="cred">Validity: <span style="color: black;">{{ hotd.date_to
                                                    }}</span></div>
                                            <div class="address">
                                                <div>
                                                    <v-icon small>mdi-map-marker</v-icon>
                                                    {{hotd.city}}, {{hotd.state}}
                                                </div>
                                                <a :href="'/deals/detail/'+hotd.hotdeal_slug" class="cred">Read More</a>
                                            </div>
                                        </v-col>
                                        <v-col md="4" cols="5">
                                            <a :href="('tel:'+ '+' + hotd.phonecode + hotd.mobile_phone)">
                                                <v-btn small color="primary" class="me-1 mb-2">
                                                    <v-icon small>mdi-phone</v-icon>Call Now</v-btn>
                                            </a>
                                            <a
                                                :href="('https://wa.me/'+ '+' + hotd.phonecode + hotd.mobile_phone+'?text=Enquiry from Bizlx : '+currentURL)">
                                                <v-btn small color="success" class="me-1 mb-2">
                                                    <v-icon small>mdi-whatsapp</v-icon>WhatsApp</v-btn>
                                            </a>
                                            <a :href="'/deals/detail/'+hotd.hotdeal_slug">
                                                <v-btn small color="info" class="me-1 mb-2">
                                                    <v-icon small>mdi-eye</v-icon>View Deal</v-btn>
                                            </a>
                                            <a :href="'/'+hotd.username" v-if="hotd.hotdealsCount > 1" class="ms-0">
                                                <v-btn text small color="black" class="font-weight-bold">Deals
                                                    ({{hotd.hotdealsCount}})</v-btn>
                                            </a>
                                        </v-col>
                                    </v-row>
                                </v-col>
                            </v-row>
                        </v-card>
                        <v-pagination v-if="hlist.length > 0" class="pagination pt-3" v-model="page"
                            :length="Math.ceil(hotds.length / perPage)" circle></v-pagination>
                    </div>
                    <div class="mobile">
                        <v-card class="item mitem my-2 mb-7" v-for="hotd in hlist" :key="hotd.hotdeal_id">
                            <a :href="'/deals/detail/'+hotd.hotdeal_slug"
                                class="small d-flex link-dark align-center text-decoration-none">
                                <a :href="'/deals/detail/'+hotd.hotdeal_slug">
                                    <div class="mimg">
                                        <v-img class="rounded" v-if="hotd.hotdeal_single_image.hotdeal_img_url != null"
                                            :src="(url+hotd.hotdeal_single_image.hotdeal_img_url)" cover
                                            height="110"></v-img>
                                        <v-img v-if="hotd.hotdeal_single_image.hotdeal_img_url == null"
                                            :src="('https://placehold.co/600x400?text=' + hotd.hot_deal_title)" cover
                                            height="110">
                                        </v-img>
                                    </div>
                                </a>
                                <div class="mdetails">
                                    <a :href="'/deals/detail/'+hotd.hotdeal_slug"
                                        class="mtitle text-decoration-none link-dark h4">
                                        {{ hotd.hot_deal_title }}
                                    </a>
                                    <div><span class="me-1">Category: {{hotd.subcat_name}}</span>
                                    </div>
                                    <div class="cblu dname">By:
                                        <a :href="'/'+hotd.username" class="text-decoration-none">
                                            {{ hotd.name }}
                                        </a>
                                    </div>
                                    <div class="cred">
                                        Price: <span style="color: black;">₹{{ hotd.price_to }}</span>
                                    </div>
                                    <div class="cred">Validity: <span style="color: black;">{{ hotd.date_to }}</span>
                                    </div>
                                    <div class="address">
                                        <div>
                                            <v-icon small>mdi-map-marker</v-icon>
                                            {{hotd.city}}, {{hotd.state}}
                                        </div>
                                        <a :href="'/deals/detail/'+hotd.hotdeal_slug" class="cred">Read More</a>
                                    </div>
                                </div>
                                <div class="mds" v-if="hotd.hotdealsCount > 1">
                                    <a :href="'/deals/detail/'+hotd.hotdeal_slug"
                                        v-if="hotd.hotdealsCount > 0 && hotd.hotdealsCount < 2" class="ms-0">
                                        <div class="font-weight-bold link-dark">
                                            <div>Deal</div>
                                            <div>({{hotd.hotdealsCount}})</div>
                                        </div>
                                    </a>
                                    <a :href="'/'+hotd.username" v-if="hotd.hotdealsCount > 1" class="ms-0">
                                        <div class="font-weight-bold link-dark">
                                            <div>Deals</div>
                                            <div>({{hotd.hotdealsCount}})</div>
                                        </div>
                                    </a>
                                </div>
                            </a>
                        </v-card>
                        <v-pagination v-if="hlist.length > 0" class="pagination pt-3" v-model="page"
                            :length="Math.ceil(hotds.length / perPage)" circle></v-pagination>
                    </div>
                </div>
                <div v-else class="text-center">
                    <div v-if="form1.subcat_id != '' || form1.city_id != '' " class="float-end me-3">
                        <div v-if="form1.city_id != ''">
                            <span v-for="loc in locs" :key="loc.city_id">
                                <span v-if="loc.city_id == form1.city_id">
                                    <span @click="ifNodata()" class="text-primary" style="cursor:pointer;">City
                                        List</span>
                                </span>
                            </span>
                        </div>
                    </div>
                    <h5 class="loading">Data not found!</h5>
                </div>
            </div> -->
               <!-- deals radar for type===0 -->
    <div v-if="dealtype_selected === 0" class="col-12 col-md-12 col-lg-8 col-xxl-9 px-0 pt-0">
    <div v-if="hlist.length > 0">
        <!-- Desktop view -->
        <div class="desktop">
        <div class="d-flex justify-content-center">
            <v-btn small @click="onMobile" color="info" class="shadow_cls">
            <v-icon v-if="dealtype_selected === 2" color="white" small class="me-1">mdi-eye</v-icon>
            <v-icon v-else color="white" small class="me-1">mdi-radio-tower</v-icon>
            <span v-if="dealtype_selected === 2">More Jobs</span>
            <span v-else>Deals Radar</span>
            </v-btn>
        </div>

        <v-card class="mb-7 small" v-for="hotd in hlist" :key="hotd.hotdeal_id">
            <v-row align="center">
            <!-- IMAGE COLUMN -->
            <v-col cols="4" class="py-0">
                <a :href="`/deals/detail/${hotd.hotdeal_slug}`">
                <!-- use mainimage if present, else placeholder -->
                <v-img
                    v-if="hotd.mainimage"
                    :src="url + hotd.mainimage"
                    cover
                    height="200"
                />
                <v-img
                    v-else
                    :src="`https://placehold.co/600x400?text=${encodeURIComponent(hotd.hot_deal_title)}`"
                    cover
                    height="200"
                />
                </a>
            </v-col>

            <!-- DETAILS COLUMN -->
            <v-col cols="8" class="py-0">
                <v-row>
                <v-col md="8" cols="7" class="py-0">
                    <a :href="`/deals/detail/${hotd.hotdeal_slug}`"
                    class="dtitle text-decoration-none link-dark h4">
                    {{ hotd.hot_deal_title }}
                    </a>
                    <div><span class="me-1">Category: {{ hotd.subcat_name }}</span></div>
                    <div class="cblu">
                    By:
                    <a :href="'/' + hotd.username" class="text-decoration-none">
                        {{ hotd.name }}
                    </a>
                    </div>
                    <div class="cred">
                    Price: <span style="color: black;">₹{{ hotd.price_to }}</span>
                    </div>
                    <div class="cred">
                    Validity: <span style="color: black;">{{ hotd.date_to }}</span>
                    </div>
                    <div class="address">
                    <div>
                        <v-icon small>mdi-map-marker</v-icon>
                        {{ hotd.city }}, {{ hotd.state }}
                    </div>
                    <a :href="`/deals/detail/${hotd.hotdeal_slug}`" class="cred">Read More</a>
                    </div>
                </v-col>

                <v-col md="4" cols="5">
                    <a :href="`tel:+${hotd.phonecode}${hotd.mobile_phone}`">
                    <v-btn small color="primary" class="me-1 mb-2">
                        <v-icon small>mdi-phone</v-icon>Call Now
                    </v-btn>
                    </a>
                    <a :href="`https://wa.me/+${hotd.phonecode}${hotd.mobile_phone}?text=Enquiry from Bizlx : ${currentURL}`">
                    <v-btn small color="success" class="me-1 mb-2">
                        <v-icon small>mdi-whatsapp</v-icon>WhatsApp
                    </v-btn>
                    </a>
                    <a :href="`/deals/detail/${hotd.hotdeal_slug}`">
                    <v-btn small color="info" class="me-1 mb-2">
                        <v-icon small>mdi-eye</v-icon>View Deal
                    </v-btn>
                    </a>
                    <a v-if="hotd.hotdealsCount > 1" :href="'/' + hotd.username" class="ms-0">
                    <v-btn text small color="black" class="font-weight-bold">
                        Deals ({{ hotd.hotdealsCount }})
                    </v-btn>
                    </a>
                </v-col>
                </v-row>
            </v-col>
            </v-row>
        </v-card>

        <v-pagination
            v-if="hlist.length"
            class="pagination pt-3"
            v-model="page"
            :length="Math.ceil(hotds.length / perPage)"
            circle
        />
        </div>

        <!-- Mobile view -->
        <div class="mobile">
        <v-card class="item mitem my-2 mb-7" v-for="hotd in hlist" :key="hotd.hotdeal_id">
            <a :href="`/deals/detail/${hotd.hotdeal_slug}`" class="small d-flex link-dark align-center text-decoration-none">
            <div class="mimg">
                <v-img
                v-if="hotd.mainimage"
                :src="url + hotd.mainimage"
                cover
                height="110"
                />
                <v-img
                v-else
                :src="`https://placehold.co/600x400?text=${encodeURIComponent(hotd.hot_deal_title)}`"
                cover
                height="110"
                />
            </div>
            <div class="mdetails">
                <a :href="`/deals/detail/${hotd.hotdeal_slug}`" class="mtitle text-decoration-none link-dark h4">
                {{ hotd.hot_deal_title }}
                </a>
                <div><span class="me-1">Category: {{ hotd.subcat_name }}</span></div>
                <div class="cblu dname">
                By:
                <a :href="'/' + hotd.username" class="text-decoration-none">
                    {{ hotd.name }}
                </a>
                </div>
                <div class="cred">Price: <span style="color: black;">₹{{ hotd.price_to }}</span></div>
                <div class="cred">Validity: <span style="color: black;">{{ hotd.date_to }}</span></div>
                <div class="address">
                <div>
                    <v-icon small>mdi-map-marker</v-icon>
                    {{ hotd.city }}, {{ hotd.state }}
                </div>
                <a :href="`/deals/detail/${hotd.hotdeal_slug}`" class="cred">Read More</a>
                </div>
            </div>
            </a>
        </v-card>
        <v-pagination
            v-if="hlist.length"
            class="pagination pt-3"
            v-model="page"
            :length="Math.ceil(hotds.length / perPage)"
            circle
        />
        </div>
    </div>

    <!-- no-data fallback -->
    <div v-else class="text-center">
        <div v-if="form1.subcat_id || form1.city_id" class="float-end me-3">
        <span v-for="loc in locs" :key="loc.city_id">
            <span v-if="loc.city_id === form1.city_id">
            <span @click="ifNodata()" class="text-primary" style="cursor:pointer;">City List</span>
            </span>
        </span>
        </div>
        <h5 class="loading">Data not found!</h5>
    </div>
    </div>

                <!-- end of deals radar for type===0  -->

                <!-- deals radar for type===1 -->
                <div v-if="dealtype_selected === 1" class="col-12 col-md-12 col-lg-8 col-xxl-9 px-0 pt-0">
                    <div v-if="slist.length > 0">
                        <div class="desktop">
                            <div class="d-flex justify-content-center">
                            <v-btn small @click="onMobile" color="info" class="shadow_cls">
                                <v-icon v-if="dealtype_selected === 2" color="white" small class="me-1">mdi-eye</v-icon>
                                <v-icon v-else color="white" small class="me-1">mdi-radio-tower</v-icon>
                                <span v-if="dealtype_selected === 2">More Jobs</span>
                                <span v-else>Deals Radar</span>
                            </v-btn>
                        </div>
                            <v-card class="mb-7 small" v-for="sal in slist" :key="sal.sale_id">
                                <v-row align="center">
                                    <v-col md="4" cols="4" class="py-0">
                                        <a :href="('/sales/detail/'+sal.sale_slug)">
                                            <v-img :src=(url+sal.sale_imageurl) cover height="200">
                                            </v-img>
                                        </a>
                                    </v-col>
                                    <v-col md="8" cols="8" class="py-0">
                                        <v-row>
                                            <v-col md="8" cols="7" class="py-0">
                                                <a :href="('/sales/detail/'+sal.sale_slug)"
                                                    class="dtitle text-decoration-none link-dark h4">
                                                    {{ sal.sale_title }}
                                                </a>
                                                <div><span class="me-1">Category: {{sal.subcat_name}}</span>
                                                </div>
                                                <div class="cblu">By:
                                                    <a :href="'/'+sal.username" class="text-decoration-none">
                                                        {{ sal.name }}
                                                    </a>
                                                </div>
                                                <div class="cred">Sale:
                                                    <del class=" ms-1 h6">₹{{ sal.normal_price }}</del>
                                                    <span class=" ms-1 h4 ">₹{{ sal.sale_price }}</span>
                                                </div>
                                                <div class="cred">Validity: <span style="color: black;">{{ sal.saledate_to
                                                        }}</span></div>
                                                <div class="address">
                                                    <div>
                                                        <v-icon small>mdi-map-marker</v-icon>
                                                        {{ sal.city }}, {{ sal.state }}
                                                    </div>
                                                    <a :href="'/sales/detail/'+sal.sale_slug" class="cred">Read More</a>
                                                </div>

                                            </v-col>
                                            <v-col md="4" cols="5">
                                                <a :href="('tel:' + '+' + sal.phonecode + sal.mobile_phone)">
                                                    <v-btn small color="primary" class="me-1 mb-2">
                                                        <v-icon small>mdi-phone</v-icon>Call Now</v-btn>
                                                </a>
                                                <a :href="('https://wa.me/' + '+' + sal.phonecode + sal.mobile_phone + '?text=Enquiry from Bizlx : '+currentURL)"
                                                    target="_blank">
                                                    <v-btn small color="success" class="me-1 mb-2">
                                                        <v-icon small>mdi-whatsapp</v-icon>WhatsApp</v-btn>
                                                </a>
                                                <a :href="'/sales/detail/'+sal.sale_slug">
                                                    <v-btn small color="info" class="me-1 mb-2">
                                                        <v-icon small>mdi-eye</v-icon>View Sale</v-btn>
                                                </a>
                                                <a :href="'/'+sal.username" v-if="sal.salesCount > 1" class="ms-0">
                                                    <v-btn text small color="black" class="font-weight-bold">Sale
                                                        ({{sal.salesCount}})</v-btn>
                                                </a>
                                            </v-col>
                                        </v-row>
                                    </v-col>
                                </v-row>
                            </v-card>
                            <v-pagination v-if="slist.length > 0" class="pagination pt-3" v-model="page"
                                :length="Math.ceil(salesd.length / perPage)" circle></v-pagination>
                        </div>
                        <div class="mobile">
                            <v-card class="item mitem my-2 mb-7" v-for="sal in slist" :key="sal.sale_id">
                                <a :href="'/sales/detail/'+sal.sale_slug" class="small d-flex link-dark align-center">
                                    <a :href="'/sales/detail/'+sal.sale_slug">
                                        <div class="mimg">
                                            <v-img class="rounded" v-if="sal.sale_imageurl != null"
                                                :src=(url+sal.sale_imageurl) cover height="110">
                                            </v-img>
                                            <v-img v-if="sal.sale_imageurl == null"
                                                src="https://placehold.co/600x400?text=deal_hot_deal_title" cover
                                                height="110">
                                            </v-img>
                                        </div>
                                    </a>
                                    <div class="mdetails">
                                        <a :href="'/sales/detail/'+sal.sale_slug"
                                            class="mtitle text-decoration-none link-dark h4">
                                            {{ sal.sale_title }}
                                        </a>
                                        <div><span class="me-1">Category: {{sal.subcat_name}}</span>
                                        </div>
                                        <div class="cblu dname">By:
                                            <a :href="'/sales/detail/'+sal.username" class="text-decoration-none">
                                                {{ sal.name }}
                                            </a>
                                        </div>
                                        <div class="cred">Sale:
                                            <del class=" ms-1 h6">₹{{ sal.normal_price }}</del>
                                            <span class=" ms-1 h4 ">₹{{ sal.sale_price }}</span>
                                        </div>
                                        <div class="cred">Validity: <span style="color: black;">{{ sal.saledate_to }}</span>
                                        </div>
                                        <div class="address">
                                            <div>
                                                <v-icon small>mdi-map-marker</v-icon>
                                                {{ sal.city }}, {{ sal.state }}
                                            </div>
                                            <a :href="'/sales/detail/'+sal.sale_slug" class="cred">Read More</a>
                                        </div>
                                    </div>
                                    <div class="mds" v-if="sal.salesCount > 1">
                                        <a :href="'/sales/detail/'+sal.sale_slug"
                                            v-if="sal.salesCount > 0 && sal.salesCount < 2" class="ms-0">
                                            <div class="font-weight-bold link-dark">
                                                <div>Sale</div>
                                                <div>({{sal.salesCount}})</div>
                                            </div>
                                        </a>
                                        <a :href="'/'+sal.username" v-if="sal.salesCount > 1" class="ms-0">
                                            <div class="font-weight-bold link-dark">
                                                <div>Sale</div>
                                                <div>({{sal.salesCount}})</div>
                                            </div>
                                        </a>
                                    </div>
                                </a>
                            </v-card>
                        </div>
                    </div>
                    <div v-else class="text-center">
                        <div v-if="form1.subcat_id != '' && form1.city_id != '' " class="float-end me-3">
                            <div v-if="form1.city_id != ''">
                                <span v-for="loc in locs" :key="loc.city_id">
                                    <span v-if="loc.city_id == form1.city_id">
                                        <span @click="ifNodata()" class="text-primary" style="cursor:pointer;">City
                                            List</span>
                                    </span>
                                </span>
                            </div>
                        </div>
                        <h5 class="loading">Data not found!</h5>
                    </div>
                </div>

                <!-- deals radar for type===1 -->

                <!-- deals radar for type===2 -->

                <div v-if="dealtype_selected === 2" class="col-12 col-md-12 col-lg-8 col-xxl-9 px-0 pt-0">
                    <div v-if="jlist.length > 0">
                        <div class="desktop">
                            <div class="d-flex justify-content-center">
                            <v-btn small @click="onMobile" color="info" class="shadow_cls">
                                <v-icon v-if="dealtype_selected === 2" color="white" small class="me-1">mdi-eye</v-icon>
                                <v-icon v-else color="white" small class="me-1">mdi-radio-tower</v-icon>
                                <span v-if="dealtype_selected === 2">More Jobs</span>
                                <span v-else>Deals Radar</span>
                            </v-btn>
                        </div>
                            <v-card class="mb-7 small" v-for="(job,i) in jlist" :key="i" :perPage="perPage">
                                <v-card-text>
                                    <v-row>
                                        <v-col cols="4">
                                            <a :href="'jobs/detail/'+job.job_slug">
                                                <v-img v-if="job.mainimage !== null" :src="url+job.mainimage"
                                                    class="rounded img-fluid" cover max-height="200"></v-img>
                                                <v-img v-else :src="url+'/images/placeholder-job.svg'"
                                                    class="rounded img-fluid" contain max-height="200"></v-img>
                                            </a>
                                        </v-col>
                                        <v-col cols="8">
                                            <v-row>
                                                <v-col cols="7" md="8">
                                                    <a :href="('jobs/detail/'+job.job_slug)"
                                                    class="dtitle text-decoration-none link-dark h4">
                                                    {{ job.job_title }}
                                                    </a>
                                                    <div v-if="job.job_cat_name == null">Category: Not Assigned</div>
                                                    <div v-else>Category: {{job.job_cat_name}}</div>
                                                    <div class="cblu">By: <a :href="'/'+job.username"
                                                            class="text-decoration-none">{{job.name}}</a></div>
                                                    <!-- <div v-if="!loggedUser">Add To Wishlist
                                                        <v-icon small>mdi-heart-outline</v-icon>
                                                    </div>
                                                    <div v-else>Add To Wishlist
                                                        <v-icon v-if="job.user_added_wishlist!=null" color="red" small>mdi-heart Added</v-icon>
                                                        <v-icon v-else small>mdi-heart-outline</v-icon>
                                                    </div> -->
                                                    <div class="fs-6"><v-icon small>mdi-map-marker</v-icon>{{job.city}},
                                                        {{job.state}}</div>
                                                    <div class="fs-6"><span class="fw-bold link-dark fs-6">Min Experience:
                                                        </span>
                                                        <span v-if="job.min_exp==6">5 + Years</span>
                                                        <span v-else>{{job.min_exp}} Years</span>
                                                    </div>
                                                    <div class="fw-bold link-dark fs-6">
                                                        ₹ {{job.salary_from}} <span class="fw-semibold">Per Month</span>
                                                    </div>
                                                    <a :href="'jobs/detail/'+job.job_slug" class="mt-2 cred fs-6">Read
                                                        More</a>
                                                </v-col>
                                                <v-col cols="5" md="4">
                                                    <a :href="('tel:'+'+'+ job.phonecode +job.mobile_phone)">
                                                        <v-btn small color="primary" class="me-1 mb-2">
                                                            <v-icon small>mdi-phone</v-icon>Call Now</v-btn>
                                                    </a>
                                                    <a :href="('https://wa.me/'+'+'+ job.phonecode+job.mobile_phone+'?text=Enquiry from Bizlx : '+currentURL)"
                                                        target="_blank">
                                                        <v-btn small color="success" class="me-1 mb-2">
                                                            <v-icon small>mdi-whatsapp</v-icon>WhatsApp</v-btn>
                                                    </a>
                                                    <a :href="'jobs/detail/'+job.job_slug">
                                                        <v-btn small color="info" class="me-1 mb-2">
                                                            <v-icon small>mdi-deal</v-icon>View Job</v-btn>
                                                    </a>
                                                    <a :href="'/'+job.username" v-if="job.jobsCount > 1" class="ms-0">
                                                        <v-btn text small color="black" class="font-weight-bold">Jobs
                                                            ({{job.jobsCount}})</v-btn>
                                                    </a>
                                                </v-col>
                                            </v-row>
                                        </v-col>
                                    </v-row>
                                </v-card-text>
                            </v-card>
                            <v-pagination v-if="jlist.length > 0" class="pagination pt-3" v-model="page"
                                :length="Math.ceil(jobsd.length / perPage)" circle></v-pagination>
                        </div>
                        <v-row class="mobile jobrow" v-if="jlist.length > 0">
                            <v-card v-for="(job,i) in jlist" :key="i" :perPage="perPage" class="item mitem jobcard">
                                <a class="small d-flex align-items-center link-dark text-decoration-none"
                                    :href="'jobs/detail/'+job.job_slug">
                                    <a :href="'jobs/detail/'+job.job_slug">
                                        <v-img v-if="job.mainimage !== null" :src="url+job.mainimage"
                                            class="rounded img-fluid" cover max-width="110" max-height="110"></v-img>
                                        <v-img v-else :src="url+'/images/placeholder-job.svg'" class="rounded img-fluid"
                                            contain max-width="110" max-height="110"></v-img>
                                    </a>
                                    <div class="mdetails">
                                        <a :href="'jobs/detail/'+job.job_slug"
                                            class="mtitle text-decoration-none link-dark h4">
                                            {{job.job_title}}
                                        </a>
                                        <div v-if="job.job_cat_name == null">Category: Not Assigned</div>
                                        <div v-else>Category: {{job.job_cat_name}}</div>
                                        <div class="cblu">By: <a :href="'/'+job.username"
                                                class="text-decoration-none">{{job.name}}</a></div>
                                        <div><v-icon small>mdi-map-marker</v-icon>{{job.city}}, {{job.state}}</div>
                                        <div>
                                            <span class="fw-bold link-dark">Min Experience></span>
                                            <span v-if="job.min_exp==6">5 + Years</span>
                                            <span v-else>{{job.min_exp}} Years</span>
                                        </div>
                                        <div class="fw-bold link-dark">
                                            ₹ {{job.salary_from}} <span class="fw-semibold">Per Month</span>
                                        </div>
                                        <a :href="'jobs/detail/'+job.job_slug" class="mt-2 cred">Read More</a>
                                    </div>
                                    <div class="mds" v-if="job.jobsCount > 1">
                                        <a :href="'jobs/detail/'+job.job_slug" v-if="job.jobsCount > 0 && job.jobsCount < 2"
                                            class="ms-0">
                                            <div class="font-weight-bold link-dark">
                                                <div>Job</div>
                                                <div>({{job.jobsCount}})</div>
                                            </div>
                                        </a>
                                        <a :href="'/'+job.username" v-if="job.jobsCount > 1" class="ms-0">
                                            <div class="font-weight-bold link-dark">
                                                <div>Jobs</div>
                                                <div>({{job.jobsCount}})</div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="mcontact">
                                        <a :href="('https://wa.me/'+job.mobile_phone+'?text=Enquiry from Bizlx : '+currentURL)"
                                            target="_blank">
                                            <v-btn fab small color="success"
                                                class="mb-2"><v-icon>mdi-whatsapp</v-icon></v-btn>
                                        </a>
                                        <a :href="('tel:'+ job.mobile_phone)">
                                            <v-btn fab small color="primary" class="mb-2"><v-icon>mdi-phone</v-icon></v-btn>
                                        </a>
                                        <a :href="'jobs/detail/'+job.job_slug">
                                            <v-btn fab small color="info" class="me-1 mb-2">
                                                <v-icon small>mdi-eye</v-icon>
                                            </v-btn>
                                        </a>
                                    </div>
                                </a>
                            </v-card>
                            <v-pagination v-if="jlist.length > 0" class="pagination pt-3" v-model="page"
                                :length="Math.ceil(jobsd.length/perPage)" circle></v-pagination>
                        </v-row>
                    </div>
                    <div v-else class="text-center">
                        <div v-if="form1.subcat_id != '' && form1.city_id != '' " class="float-end me-3">
                            <div v-if="form1.city_id != ''">
                                <span v-for="loc in locs" :key="loc.city_id">
                                    <span v-if="loc.city_id == form1.city_id">
                                        <span @click="ifNodata()" class="text-primary" style="cursor:pointer;">City
                                            List</span>
                                    </span>
                                </span>
                            </div>
                        </div>
                        <h5 class="loading">Data not found!</h5>
                    </div>
                </div>

                <!-- deals radar for type===2 -->

                <!-- deals radar for type===3 -->
                <div v-if="dealtype_selected === 3 || dealtype_selected === 4"
                    class="col-12 col-md-12 col-lg-8 col-xxl-9 px-0 pt-0">
                    <div v-if="alist.length > 0">
                        <div class="desktop">
                            <div class="d-flex justify-content-center">
                            <v-btn small @click="onMobile" color="info" class="shadow_cls">
                                <v-icon v-if="dealtype_selected === 2" color="white" small class="me-1">mdi-eye</v-icon>
                                <v-icon v-else color="white" small class="me-1">mdi-radio-tower</v-icon>
                                <span v-if="dealtype_selected === 2">More Jobs</span>
                                <span v-else>Deals Radar</span>
                            </v-btn>
                        </div>
                            <v-card class="mb-7 small" v-for="buss in alist" :key="buss.id">
                                <v-row align="center">
                                    <v-col cols="4" class="py-0">
                                        <a :href="'/'+buss.username">
                                            <v-img v-if="buss.mainimage != null" :src="(url+buss.mainimage)" contain
                                                :aspect-ratio="1/1" max-height="200"></v-img>
                                            <v-img v-if="buss.mainimage == null"
                                                :src="('https://placehold.co/400x400?text=' + buss.name)" contain
                                                :aspect-ratio="1/1" max-height="200">
                                            </v-img>
                                        </a>
                                    </v-col>
                                    <v-col cols="8" class="py-0">
                                        <v-row>
                                            <v-col md="8" cols="7" class="py-0">
                                                <a :href="'/'+buss.username"
                                                    class="dtitle text-decoration-none link-dark h4">
                                                    {{ buss.name }}
                                                </a>
                                                <div>
                                                    <span class="me-1">Category: {{buss.subcat_name}}</span>
                                                </div>
                                                <div class="mreviews d-flex align-items-center">
                                                    <v-rating dense color="warning" size="16" length="5"
                                                        :value=buss.rating_avrage readonly></v-rating>
                                                    <span class="badge bg-dark ms-1">{{ buss.rating_avrage }}</span>
                                                </div>
                                                <div class="address">
                                                    <div>
                                                        <v-icon small color="conblue">mdi-map-marker</v-icon>
                                                        {{buss.city}}, {{buss.state}}
                                                    </div>
                                                    <a :href="'/'+buss.username" class="cred">Read More</a>
                                                </div>

                                            </v-col>
                                            <v-col md="4" cols="5">
                                                <a :href="('tel:'+'+'+buss.phonecode+buss.mobile_phone)">
                                                    <v-btn small color="primary" class="me-1 mb-2">
                                                        <v-icon small>mdi-phone</v-icon>Call Now</v-btn>
                                                </a>
                                                <a
                                                    :href="('https://wa.me/'+'+'+buss.phonecode+buss.mobile_phone+'?text=Enquiry from Bizlx : '+currentURL)">
                                                    <v-btn small color="success" class="me-1 mb-2">
                                                        <v-icon small>mdi-whatsapp</v-icon>WhatsApp</v-btn>
                                                </a>
                                                <a :href="'/'+buss.username">
                                                    <v-btn small color="info" class="me-1 mb-2">
                                                        <v-icon small>mdi-eye</v-icon>View</v-btn>
                                                </a>
                                            </v-col>
                                        </v-row>
                                    </v-col>
                                </v-row>
                            </v-card>
                            <v-pagination v-if="alist.length > 0" class="pagination pt-3" v-model="page"
                                :length="Math.ceil(allists.length / perPage)" circle></v-pagination>
                        </div>
                        <div class="mobile">
                            <v-card class="item mitem my-2 mb-7" v-for="buss in alist" :key="buss.id">
                                <a :href="'/'+buss.username" class="small d-flex link-dark align-center">
                                    <a :href="'/'+buss.username">
                                        <div class="mimg">
                                            <v-img class="rounded" v-if="buss.mainimage != null" :src="(url+buss.mainimage)"
                                                cover height="110"></v-img>
                                            <v-img v-if="buss.mainimage == null"
                                                :src="('https://placehold.co/600x400?text=' + buss.name)" cover
                                                height="110">
                                            </v-img>
                                        </div>
                                    </a>
                                    <div class="mdetails">
                                        <a :href="'/'+buss.username" class="mtitle text-decoration-none link-dark h4">
                                            {{ buss.name }}
                                        </a>
                                        <div><span class="me-1">Category: {{buss.subcat_name}}</span>
                                        </div>
                                        <div class="mreviews d-flex align-items-center">
                                            <v-rating dense color="warning" size="16" length="5" :value=buss.rating_avrage
                                                readonly></v-rating>
                                            <span class="badge bg-dark ms-1">{{ buss.rating_avrage }}</span>
                                        </div>
                                        <div class="address">
                                            <div>
                                                <v-icon small>mdi-map-marker</v-icon>
                                                {{buss.city}}, {{buss.state}}
                                            </div>
                                            <a :href="'/'+buss.username" class="cred">Read More</a>
                                        </div>
                                    </div>
                                </a>
                            </v-card>
                            <v-pagination v-if="alist.length > 0" class="pagination pt-3" v-model="page"
                                :length="Math.ceil(allists.length / perPage)" circle></v-pagination>
                        </div>
                    </div>
                    <div v-else class="text-center">
                        <div v-if="form1.subcat_id != '' || form1.city_id != '' " class="float-end me-3">
                            <div v-if="form1.city_id != ''">
                                <span v-for="loc in locs" :key="loc.city_id">
                                    <span v-if="loc.city_id == form1.city_id">
                                        <span @click="ifNodata()" class="text-primary" style="cursor:pointer;">City
                                            List</span>
                                    </span>
                                </span>
                            </div>
                        </div>
                        <h5 class="loading">Data not found!</h5>
                    </div>
                </div>

                <!-- deals radar for type===3 -->

                <!-- deals radar for type===5 -->

                <div v-if="dealtype_selected === 5" class="video col-12 col-md-12 col-lg-8 col-xxl-9 px-0 pt-0">
                    <div v-if="vlist.length > 0">
                        <!-- Desktop Layout -->
                        <div class="desktop">
                            <div class="d-flex justify-content-center">
                            <v-btn small @click="onMobile" color="info" class="shadow_cls">
                                <v-icon v-if="dealtype_selected === 2" color="white" small class="me-1">mdi-eye</v-icon>
                                <v-icon v-else color="white" small class="me-1">mdi-radio-tower</v-icon>
                                <span v-if="dealtype_selected === 2">More Jobs</span>
                                <span v-else>Deals Radar</span>
                            </v-btn>
                        </div>
                            <v-card class="mb-7 small" v-for="video in vlist" :key="video.video_id">
                                <v-row align="center">
                                    <v-col cols="4" class="py-0">
                                        <a :href="'/videos/detail/' + video.video_slug">
                                            <v-img v-if="video.thumbnail_url" :src="video.thumbnail_url" contain
                                                max-height="200" class="me-3"></v-img>
                                            <v-img v-else src="https://placehold.co/150x150?text=No+Image" contain
                                                max-height="200" class="me-3"></v-img>
                                        </a>
                                    </v-col>
                                    <v-col cols="8" class="py-0">
                                        <v-row>
                                            <v-col md="8" cols="7" class="py-0">
                                                <a :href="'/videos/detail/' + video.video_slug"
                                                    class="dtitle text-decoration-none link-dark h4">
                                                    {{ video.video_title }}
                                                </a>
                                                <div>
                                                    <span class="me-1">Category: {{ video.subcat_name }}</span>
                                                </div>
                                                <div class="cblu">By: <a :href="'/'+video.username"
                                                    class="text-decoration-none">{{video.name}}</a></div>
                                                <!-- <div class="mreviews d-flex align-items-center">
                                                    <v-rating dense color="warning" size="16" length="5"
                                                        :value="video.rating_avrage" readonly></v-rating>
                                                    <span class="badge bg-dark ms-1">{{ video.rating_avrage }}</span>
                                                </div> -->
                                                <div class="address">
                                                    <v-icon small color="conblue">mdi-map-marker</v-icon>
                                                    {{ video.city }}, {{ video.state }}
                                                </div>
                                                <a :href="'/videos/detail/' + video.video_slug" class="cred">Read More</a>
                                            </v-col>
                                            <v-col md="4" cols="5">
                                                <a :href="'tel:' +'+'+ video.phonecode + video.mobile_phone">
                                                    <v-btn small color="primary" class="me-1 mb-2">
                                                        <v-icon small>mdi-phone</v-icon>Call Now</v-btn>
                                                </a>
                                                <a
                                                    :href="'https://wa.me/' +'+'+ video.phonecode + video.mobile_phone + '?text=Enquiry from Bizlx : ' + currentURL">
                                                    <v-btn small color="success" class="me-1 mb-2">
                                                        <v-icon small>mdi-whatsapp</v-icon>WhatsApp</v-btn>
                                                </a>
                                                <a :href="'/videos/detail/' + video.video_slug">
                                                    <v-btn small color="info" class="me-1 mb-2">
                                                        <v-icon small>mdi-eye</v-icon>View</v-btn>
                                                </a>
                                            </v-col>
                                        </v-row>
                                    </v-col>
                                </v-row>
                            </v-card>
                        </div>

                        <!-- Mobile Layout -->
                        <v-container fluid>
                            <v-row class="mobile" v-if="vlist.length > 0" justify="center" align="start" no-gutters>
                                <v-col cols="12" sm="8" md="6" lg="4" v-for="video in vlist" :key="video.video_id"
                                    class="mb-4">
                                    <v-card class="w-100 p-3 d-flex flex-column align-start">
                                        <v-row no-gutters class="w-100">
                                            <!-- Video Image -->
                                            <v-col cols="4" class="d-flex justify-center video-contant">
                                                <a :href="'/videos/detail/' + video.video_slug">
                                                    <v-img v-if="video.thumbnail_url" :src="video.thumbnail_url" cover
                                                        height="100" width="100" class="rounded"></v-img>
                                                    <v-img v-else src="https://placehold.co/100x100?text=No+Image" cover
                                                        height="100" width="100" class="rounded"></v-img>
                                                </a>
                                            </v-col>
                                            <!-- Video Details -->
                                            <v-col cols="8" class="d-flex flex-column">
                                                <a :href="'/videos/detail/' + video.video_slug"
                                                    class="mtitle text-decoration-none link-dark font-weight-bold mb-2">
                                                    {{ video.video_title }}
                                                </a>
                                                <div v-if="video.subcat_name == null">Category: Not Assigned</div>
                                                <div v-else>Category: {{video.subcat_name}}</div>
                                                <div class="cblu">By: <a :href="'/'+video.username"
                                                    class="text-decoration-none">{{video.name}}</a></div>
                                                <div class="address d-flex align-items-center">
                                                    <v-icon small class="me-1">mdi-map-marker</v-icon>
                                                    <span>{{ video.city }}, {{ video.state }}</span>
                                                </div>
                                                <!-- <div class="mreviews d-flex align-items-center mt-1">
                                                    <v-rating dense color="warning" size="12" length="5"
                                                        :value="video.rating_avrage" readonly></v-rating>
                                                    <span class="badge bg-dark ms-2">{{ video.rating_avrage }}</span>
                                                </div> -->
                                                <a :href="'/videos/detail/' + video.video_slug"
                                                    class="cred">
                                                    Read More
                                                </a>
                                            </v-col>
                                        </v-row>
                                    </v-card>
                                </v-col>
                            </v-row>
                            <!-- Pagination -->
                            <v-pagination v-if="vlist.length > 0" class="pagination pt-3" v-model="page"
                                :length="Math.ceil(vlist.length  / perPage)" circle></v-pagination>
                        </v-container>



                    </div>
                    <div v-else class="text-center">
                        <h5 class="loading">Data not found!</h5>
                    </div>
                </div>

                <!-- deals radar for type===5 -->



        </div>
<!-- deal radar -->
        <v-dialog v-model="dialog2" style="z-index: 9999;" max-width="375px">
            <v-card>
                <div class="d-flex justify-content-between p-2">
                    <span class="mx-2 mt-1 mb-0" style="width: 100px">
                        <v-select v-model="form1.country_id" :items="countries" item-value="id" item-text="country"
                            hide-details dense>
                        </v-select>
                    </span>
                    <v-icon @click="dialog2 = false" class="me-3">mdi-close</v-icon>
                </div>
                <hr class="m-0">
                <v-autocomplete v-if="form1.dealtype_selected == 2" class="px-2 pt-3"  v-model="form1.jobcat_id"
                    placeholder="Job Type" dense hide-details label="Job Type" clearable :items="jobs"
                    item-text="job_cat_name" item-value="id" @keyup.native="jobCategoryKeyUp">
                </v-autocomplete>
                <v-autocomplete
                    v-if="form1.dealtype_selected == 0 || form1.dealtype_selected == 1 || form1.dealtype_selected == 3 || form1.dealtype_selected == 4"
                    class="px-2 pt-3"  v-model="form1.subcat_id" dense hide-details placeholder="Category"
                    label="Category" clearable :items="subcats" item-text="subcat_name" item-value="id"
                    @click.native="categoriesInputEvent" @keyup.native="categoriesKeyUp" @change="onCatChange"
                    @click:clear="onCatClear">
                </v-autocomplete>
                <v-autocomplete class="px-2 pt-3 pb-2" id="f14" v-model="form1.city_id" dense hide-details
                    placeholder="Location" label="Location" clearable :items="locs" item-text="city"
                    item-value="city_id" @keyup.native="locationKeyUp" @change="onCityChange"
                    @click:clear="onCityClear">
                </v-autocomplete>
               <!-- in your template -->
                <v-autocomplete
                class="px-2 pb-2"
                v-model="form1.area"               
                :search-input.sync="areaSearch"
                :items="areas"
                item-text="name"
                item-value="name"
                :filter="() => true"
                :loading="loadingAreas"
                no-data-text="No nearby areas found"
                clearable
                placeholder="Nearby Areas"
                label="Nearby Areas"
                @update:search-input="debouncedAreaSearch"  
                @change="onAreaChange"
                @click:clear="onAreaClear"                 
                />
<!-- Mobile popup -->
<div
  id="dealtypem"
  class="d-flex justify-content-between bg-white px-2 ps-2 pt-2 pb-3 mb-0 border-bottom border-dark border-opacity-50"
>
  <div
    class="form-check"
    v-for="d1 in dealtype"
    :key="d1.id"
    :class="d1.bclas"
  >
    <input
      class="form-check-input"
      type="radio"
      :name="'dealtype-mobile'"
      :id="`dealtypem-${d1.id}`"
      :value="d1.id"
      v-model="form1.dealtype_selected"
    >
    <label
      class="form-check-label"
      :for="`dealtypem-${d1.id}`"
    >
      {{ d1.name }}
    </label>
  </div>
</div>


                <div class="sec10 bg-white my-2">
                    <input type="text" class="form-control" placeholder="Keyword Search" v-model="form1.searchy">
                </div>
                <div v-if="form1.dealtype_selected === 2" class="h6 sec10">Salary Range</div>
                <div v-if="form1.dealtype_selected === 1 || form1.dealtype_selected === 0" class="h6 sec10">Price Range
                </div>
                <div class="range-slider sec10 bg-white px-2"
                    v-if="form1.dealtype_selected === 2 || form1.dealtype_selected === 0 || form1.dealtype_selected === 1">
                    <span class="d-flex align-items-center">
                        <input type="number" v-model="form1.min_price" class="form-control me-2 text-center"
                            placeholder="From">
                        <input type="number" v-model="form1.max_price" class="form-control ms-2 text-center"
                            placeholder="To">
                    </span>
                </div>
                <hr class="m-0">
                <div
                    v-if="form1.dealtype_selected === 0 || form1.dealtype_selected === 1 || form1.dealtype_selected === 3 || form1.dealtype_selected === 4 || form1.dealtype_selected === 5">
                    <div v-if="form1.city_id && form1.dealtype_selected === 0" class="h6 sec2">Deals Near By</div>
                    <div v-if="form1.city_id && form1.dealtype_selected === 1" class="h6 sec2">Sales Near By</div>
                    <div v-if="form1.city_id && form1.dealtype_selected === 3 || form1.city_id && form1.dealtype_selected === 4"
                        class="h6 sec2">List Near By</div>
                    <v-slider v-if="form1.city_id" class="px-4 mt-10" v-model="form1.radius1" :thumb-color="ex3.color"
                        :max="ex3.val" thumb-label="always" label="KM" inverse-label>
                    </v-slider>
                    <div class="text-center px-2">
                        <v-btn color="success" small @click="getNewdata(true)" class="my-2 ms-10">Go</v-btn>
                        <v-btn color="primary" small @click="resetFilter" class="mt-2 float-end">Reset</v-btn>
                    </div>
                </div>
                <div v-if="form1.dealtype_selected === 2" class="text-center px-2">
                    <v-btn color="success" small @click="getNewdata(true)" class="my-2 ms-10">Go</v-btn>
                    <v-btn color="primary" small @click="resetFilter" class="mt-2 float-end">Reset</v-btn>
                </div>
            </v-card>
        </v-dialog>
        <!-- deal radar -->
    </div>
</template>

<script>
import debounce from 'lodash/debounce'

import jquery from 'jquery';
import carousel from "vue-owl-carousel";
export default {
    name: "SearchPage",
    props:{
        reqparams: {
            type: Number,
            default: null,
        }
    },
    components: { carousel },
    data() {
        return {
            categorySearch:'',
            page: 1,
            picd:'https://source.unsplash.com/random/400x200?sig=',
            picm:'https://source.unsplash.com/random/200x200?sig=',
            localSubcatName: '',
            localSubcat_id: null,
            localCityName: '',
            localJobcatName: '',
            perPage: 20,
            url: 'https://bizlx.s3.ap-south-1.amazonaws.com',
            countries:[],
            select_country:"India",
            cat_selected:'',
            loc_selected:'',
            maxprice:'',
            subcats:[],
            jobs:[],
            jobcats:[],
            locs:[],
            areas: [],
         
            loadingAreas: false,
            scits:[],
            dealtype_selected: 3,
            dealtype:[{id:4,name:"List",bclas:"d-block"},{id:3,name:"List",bclas:"d-none"},{id:0,name:"Hot Deals"},{id:1,name:"Sales"},{id:2,name:"Jobs"},{ id: 5, name: "Video", bclas: "d-none" }
            ],
            ex3: { label: 'thumb-color', val: this.$store.state.listingradius +"km", color: 'primary' },
            dialog2:false,
            hotds:[],
            salesd:[],
            jobsd:[],
            slists:[],
            videos: [],
            vdata: [],
            videoData: [],
            
            allists:[],
            ipAddress : '',
            form1:{
                dealtype_selected: this.reqparams,
                city_id:this.$store.state.city_id,
                area: '',
                subcat_id:this.$store.state.subcat_id,
                jobcat_id:this.$store.state.jobcat_id,
                country_id:1,
                min_price:'',
                max_price:'',
                radius1:0,
                searchy:this.$store.state.searchy,
            },
            currentURL:'',
            features:[],
            carouselKey:0
        }
    },
    watch: {
  areaSearch (val) {
    if (!val) {
      this.areas = []
    }
  },
    // whenever the selected city changes, refetch featured businesses
    'form1.city_id'(newCity) {
      this.fetchFeaturedBusiness(newCity)
    },
    'form1.area'(val) {
    this.areaSearch = val || ''
  },
   'form1.dealtype_selected'(val) {
   
    const prev = this.form1.area
    this.areaSearch = prev
    this.form1.area   = prev
  }
   
    
  },
    
  created() {
  this.debouncedAreaSearch = debounce(this.areaKeyUp, 1000);



  // restore dealtype from prop or localStorage
  if (this.reqparams !== null && typeof this.reqparams !== 'undefined') {
    this.form1.dealtype_selected = this.reqparams;
  } else {
    const localStorageValue = localStorage.getItem('dealtype');
    this.form1.dealtype_selected = localStorageValue !== null
      ? Number(localStorageValue)
      : 3;
  }

  // load countries dropdown
  this.getCountry();

  // load any previously‐saved search values
  let raw = localStorage.getItem('catSearchedValues');
  let localObject;
  try {
    localObject = raw && raw !== 'undefined'
      ? JSON.parse(raw)
      : {};
  } catch (e) {
    console.warn('Could not parse catSearchedValues:', raw);
    localObject = {};
  }

  this.localSubcatName  = localObject.selectSubCatname || '';
  this.localCityName    = localObject.selectCityname  || '';
  this.localJobcatName  = localObject.jobcat_name      || '';

  // if we have a saved city _ID_, restore it and fetch featured immediately
  if (localObject.selectCity) {
    this.form1.city_id = localObject.selectCity;
    this.fetchFeaturedBusiness(this.form1.city_id);
  }

  // still prime the autocomplete lists by name if you want
  if (this.localSubcatName) this.onAutocompleteInput(this.localSubcatName);
  if (this.localCityName)   this.getLocs(this.localCityName);
  if (this.localJobcatName) this.jobCategory(this.localJobcatName);

  // any popup flag?
  const localpopup = localStorage.getItem('popup');
  if (localpopup === 'true') {
    this.dialog2 = true;
    localStorage.removeItem('popup');
  }

  this.currentURL = this.getCurrentURL();
},

    methods:{
        onAreaPicked(value) {
  // keep the input box filled with the selected area
  this.form1.area = value || '';    
},

onCityChange(val) {
    // whenever v-model (form1.city_id) changes…
    if (!val) {
      // user cleared it → wipe both locs *and* areas
      this.locs = []
      this.areas = []
      this.form1.area = ''
      this.areaSearch = ''
    }
  },

  onCityClear() {
    // when the clear “×” is clicked on City
    this.locs = []
    this.areas = []
    this.form1.city_id = ''
    this.form1.area   = ''
    this.areaSearch   = ''
  },
  onCatChange(val) {
    // whenever the v-model (form1.subcat_id) changes…
    if (!val) {
      // user cleared selection → wipe out dropdown items
      this.subcats = []
    }
  },

  onCatClear() {
    // when the little “×” is clicked
    this.subcats = []
    this.form1.subcat_id = ''
  },

        fetchFeaturedBusiness(city_id) {
      if (!city_id) {
        // no city selected → clear
        this.features = []
        return
      }
     
      console.log('[SearchPage] fetching featured for city', city_id)
      axios
        .get('/api/business/featured', { params: { city_id } })
        .then(resp => {
          // adapt to whatever your API really returns:
          this.features = resp.data.businessFeature || []
          this.resetCarousel()
        })
        .catch(err => {
          console.error('[SearchPage] featured/business error', err)
          this.features = []
        })
    },
        ifNodata(){
            this.form1.dealtype_selected = 3;
            this.form1.subcat_id = '';
            this.form1.jobcat_id = '';
            this.form1.searchy = '';
            this.form1.min_price = '';
            this.form1.max_price = '';
            this.form1.radius1 = 0;
            this.getNewdata();
        },
        callNow(phone) {
            if (phone) {
            window.location.href = `tel:${phone}`;
            } else {
            alert("Phone number is not available");
            }
        },
        onAutocompleteInput(Catvalue) {
            var trimmedValue = Catvalue.trim(); // Trim to remove leading and trailing spaces
            if (trimmedValue.length > 0) {
                axios.get(`/api/keyword/subcategories?keyword=${trimmedValue}`)
                    .then((resp) => {
                        // Check if the input value is still not an empty string
                        if (trimmedValue.length > 0) {
                            this.subcats = resp.data;
                            console.log("hbfhjkd=====",this.subcats);
                            const subcatid = this.form1.subcat_id;
                            console.log("hbfhjkd=====",subcatid);

                            const sst = typeof subcatid;
                            console.log("hbfhjkd=====",sst);

                            if (sst === "string"){
                                this.form1.subcat_id = parseInt(subcatid, 10);
                                console.log("hbfhjkd=====",this.form1.subcat_id);
                            }
                            else{
                                this.form1.subcat_id = subcatid;
                                console.log("hbfhjkd11=====",this.form1.subcat_id);
                            }
                        } else {
                            console.log('Input value is now empty, ignoring response data');
                        }
                    })
                    .catch((error) => {
                        console.error('Error fetching subcategories:', error);
                    });
            } else {
                // Handle case when value is an empty string
                console.log('Value is an empty string');
                this.subcats = []; // Set subcats to empty array or handle it as needed
            }
        },
        // function for category input key ( function start )
        categoriesKeyUp(event) {
        
        var value = event.target.value.trim(); // Trim to remove leading and trailing spaces
        if (value.length >= 0) {
            axios.get(`/api/keyword/subcategories?keyword=${value}`)
                .then((resp) => {
                    // Check if the input value is still not an empty string
                    if (event.target.value.trim().length >= 0) {
                        this.subcats = resp.data;
                        console.log(this.subcats);
                        const subcatid = this.form1.subcat_id;
                        const sst = typeof subcatid;
                        if (sst === "string"){
                            this.form1.subcat_id = parseInt(subcatid, 10);
                        }
                        else{
                            this.form1.subcat_id = subcatid;
                        }
                    } else {
                        console.log('Input value is now empty, ignoring response data');
                    }
                })
                .catch((error) => {
                    console.error('Error fetching subcategories:', error);
                });
        } else {
            // Handle case when value is an empty string
            console.log('Value is an empty string');
            this.subcats = []; // Set subcats to empty array or handle it as needed
        }
        },
        // function for category input key ( function end )
        categoriesInputEvent(event) {
            var value = event.target.value.trim(); // Trim to remove leading and trailing spaces
            if (value.length >= 0) {
                axios.get(`/api/keyword/subcategories?keyword=${value}`)
                    .then((resp) => {
                        // Check if the input value is still not an empty string
                        if (event.target.value.trim().length >= 0) {
                            this.subcats = resp.data;
                            console.log(this.subcats);
                            const subcatid = this.form1.subcat_id;
                            const sst = typeof subcatid;
                            if (sst === "string"){
                                this.form1.subcat_id = parseInt(subcatid, 10);
                            }
                            else{
                                this.form1.subcat_id = subcatid;
                            }
                        } else {
                            console.log('Input value is now empty, ignoring response data');
                        }
                    })
                    .catch((error) => {
                        console.error('Error fetching subcategories:', error);
                    });
            } else {
                // Handle case when value is an empty string
                console.log('Value is an empty string');
                this.subcats = []; // Set subcats to empty array or handle it as needed
            }
        },
        // function for location input key ( function start )
        locationKeyUp(event) {
            var country_id = this.form1.country_id;
            var value = event.target.value.trim(); // Trim to remove leading and trailing spaces
            if (value.length > 0) {
                axios.get(`/api/keyword/citybycountry?country_id=${country_id}&keyword=${value}`)
                .then((resp)=>{
                        // Check if the input value is still not an empty string
                        if (event.target.value.trim().length > 0) {
                            this.locs = resp.data.cities;
                            console.log(this.locs);
                        } else {
                            console.log('Input value is now empty, ignoring response data');
                        }
                    })
                    .catch((error) => {
                        console.error('Error fetching subcategories:', error);
                    });
            } else {
                // Handle case when value is an empty string
                console.log('Value is an empty string');
                this.locs = []; // Set subcats to empty array or handle it as needed
            }
        },
        // function for location input key ( function end )
        getCountry(){
        axios.get('/api/countries')
            .then((resp)=>{
                this.countries = resp.data.countries;
            })
        },
        jobCategory(Jobvalue) {
            var trimmedValue = Jobvalue.trim(); // Trim to remove leading and trailing spaces
            console.log("Input value:", trimmedValue);
            if (trimmedValue.length > 0) {
                axios.get(`/api/keyword/jobcategory?keyword=${trimmedValue}`)
                .then((resp) =>{
                    if (trimmedValue.length > 0) {
                    this.jobs =  resp.data.jobcategories;
                    console.log(this.jobs);
                    } else {
                    console.log('Input value is now empty, ignoring response data');
                    }
                }).catch((error) => {
                        console.error('Error fetching subcategories:', error);
                });
            }else {
                // Handle case when value is an empty string
                console.log('Value is an empty string');
                this.jobs = []; // Set subcats to empty array or handle it as needed
            }
        },
        // function for job categories input key ( function start )
        jobCategoryKeyUp(event) {
            var jobcat_id = this.form1.jobcat_id;
            if(jobcat_id){
                this.form1.jobcat_id = jobcat_id;
            }
            this.form1.jobcat_id = jobcat_id;
            var value = event.target.value.trim(); // Trim to remove leading and trailing spaces
            if (value.length > 0) {
                axios.get(`/api/keyword/jobcategory?keyword=${value}`)
                .then((resp)=>{
                        // Check if the input value is still not an empty string
                        if (event.target.value.trim().length > 0) {
                            this.jobs =  resp.data.jobcategories;
                            console.log(this.jobs);
                        } else {
                            console.log('Input value is now empty, ignoring response data');
                        }
                    })
                    .catch((error) => {
                        console.error('Error fetching subcategories:', error);
                    });
            } else {
                // Handle case when value is an empty string
                console.log('Value is an empty string');
                this.jobs = []; // Set subcats to empty array or handle it as needed
            }
        },
        // function for job categories input key ( function end )
        getLocs(locvalue){
            var country_id = this.form1.country_id;
            var trimmedValue = locvalue.trim(); // Trim to remove leading and trailing spaces
                if (trimmedValue.length > 0) {
                axios.get(`/api/keyword/citybycountry?country_id=${country_id}&keyword=${trimmedValue}`)
                .then((resp)=>{
                    if (trimmedValue.length > 0) {
                    this.locs = resp.data.cities;
                    console.log(" efkjewhfwjefhwesgw=======",this.locs);
                    } else {
                            console.log('Input value is now empty, ignoring response data');
                    }
                }).catch((error) => {
                        console.error('Error fetching subcategories:', error);
                });
            } else {
                // Handle case when value is an empty string
                console.log('Value is an empty string');
                this.locs = []; // Set subcats to empty array or handle it as needed
            }
        },
        // getNewdata( popup ){
        //     const config = {headers: { 'content-type': 'multipart/form-data' }};
        //     var city_id = this.form1.city_id;
        //     var dealselect = this.form1.dealtype_selected;
        //     var jobcat_id = this.form1.jobcat_id;
        //     var subcat_id = this.form1.subcat_id;
        //     var ssearch = this.form1.searchy;
        //     //check city
        //     if(city_id === null){
        //         city_id = "";
        //     } else {
        //         city_id = this.form1.city_id;
        //     }
        //     //check jobcat_id
        //     if(jobcat_id === null){
        //         jobcat_id = "";
        //     } else {
        //         jobcat_id = this.form1.jobcat_id;
        //     }
        //     //check subcat_id
        //     if(subcat_id === null || subcat_id === undefined){
        //         console.log('subcat_id',subcat_id);
        //         subcat_id = "";
        //     } else {
        //         if (isNaN(subcat_id)){
        //         subcat_id= '';
        //         this.form1.subcat_id = subcat_id;
        //         this.$store.dispatch('setsubcatid',{subcat_id});
        //         }
        //         else {
        //         if(dealselect === 2){
        //             subcat_id = '';
        //             this.$store.dispatch('setsubcatid',{subcat_id});
        //             this.form1.subcat_id = subcat_id;
        //         }else{
        //             subcat_id = this.form1.subcat_id;
        //         }
        //         }
        //     }
        //     //check dealselect
        //     if(dealselect === 0 || dealselect === 1 || dealselect === 3 || dealselect == 5){
        //         jobcat_id = "";
        //         this.form1.jobcat_id = jobcat_id;
        //     }
        //     if(dealselect === 4){
        //         dealselect = 3;
        //         jobcat_id = "";
        //         this.form1.jobcat_id = jobcat_id;
        //     }
        //     if(dealselect === 5){
        //         dealselect = 5;
        //         jobcat_id = "";
        //     }
        //     //check ssearch
        //     if(ssearch === undefined){
        //         ssearch = '';
        //         this.$store.dispatch('setsearchy',{ssearch});
        //     }else{
        //         ssearch = this.form1.searchy;
        //     }

        //     var fdata= {
        //         type:dealselect,
        //         country_id:this.form1.country_id,
        //         min_price:this.form1.min_price,
        //         max_price:this.form1.max_price,
        //         ip:this.ipAddress,
        //         radius:this.form1.radius1,
        //     };

        //     console.log(fdata);
        //     if(dealselect === 5){
        //         var localObject = JSON.parse(localStorage.getItem('catSearchedValues'));

        //         // Initialize query parameters object
        //         let queryParams = {};

        //         // Check if subcatId and cityId are present and add them to the queryParams
        //         if (localObject && localObject.selectSubCategoty) {
        //             queryParams.subcat_id = localObject.selectSubCategoty;
        //         }

        //         if (localObject && localObject.selectCity) {
        //             queryParams.city_id = localObject.selectCity;
        //         }

        //         // Make the GET request with conditional query parameters
        //         axios.get('/api/video/all', { params: queryParams })
        //             .then((response) => {
        //                 // Store the videos data in your Vue.js component's data
        //                 this.videos = response.data.videos;
        //                 this.dealtype_selected = 5;
        //             })
        //             .catch(error => {
        //                 console.error("Error fetching all video data:", error);
        //             });

        //     } else {
        //         axios.get(`/api/filter/data?type=${dealselect}&subcat_id=${subcat_id}&job_cat_id=${jobcat_id}
        //     &city_id=${city_id}&country_id=${fdata.country_id}&min_price=${fdata.min_price}
        //     &max_price=${fdata.max_price}&radius=${fdata.radius}&ip=${fdata.ip}&searchy=${ssearch}`,config)
        //         .then((resp)=>{
        //             this.maxprice = resp.data.maxPrice;
        //             if(resp.data.searchType == 0){
        //             this.hotds = resp.data.filter_data;
        //             this.dealtype_selected = 0;
        //             }
        //             if(resp.data.searchType == 1){
        //             this.salesd = resp.data.filter_data;
        //             this.dealtype_selected = 1;
        //             }
        //             if(resp.data.searchType == 2){
        //             this.jobsd = resp.data.filter_data;
        //             this.dealtype_selected = 2;
        //             }
        //             if(resp.data.searchType == 3){
        //             this.allists = resp.data.filter_data;
        //             this.dealtype_selected = 3;
        //             }
        //             if( popup == true && popup != undefined){
        //                 this.dialog2 = false;
        //             }
        //         })

        //     }
        //     axios.get(`/api/business/featured?city_id=${city_id}`)
        //         .then((resp) => {
        //             this.features = resp.data.businessFeature;
        //             this.resetCarousel();
        //             console.log('this.features',this.features);
        //         });

        // },
        preserveArea() {
    this.areaSearch = this._lastArea
    this.form1.area = this._lastArea
  },
        getNewdata(popup) {
            this._lastArea = this.form1.area
            const selectedArea = this.form1.area;
    const config = { headers: { 'content-type': 'multipart/form-data' } }

    let city_id     = this.form1.city_id
    let dealselect  = this.form1.dealtype_selected
    let jobcat_id   = this.form1.jobcat_id
    let subcat_id   = this.form1.subcat_id
    let ssearch     = this.form1.searchy


    // normalize city_id
    if (city_id == null) city_id = ''

    // normalize jobcat_id
    if (jobcat_id == null) jobcat_id = ''

    // normalize subcat_id
    if (subcat_id == null || subcat_id === undefined) {
      subcat_id = ''
    } else if (isNaN(subcat_id) || dealselect === 2) {
      subcat_id = ''
      this.form1.subcat_id = ''
      this.$store.dispatch('setsubcatid', { subcat_id })
    }

    // clear jobcat_id on non-jobs
    if ([0,1,3,5].includes(dealselect)) {
      jobcat_id = ''
      this.form1.jobcat_id = ''
    }
    if (dealselect === 4) {
      dealselect = 3
      jobcat_id  = ''
      this.form1.jobcat_id = ''
    }

    // normalize ssearch
    if (ssearch === undefined) {
      ssearch = ''
      this.$store.dispatch('setsearchy', { ssearch })
    } else {
      ssearch = this.form1.searchy
    }

    const fdata = {
      type:       dealselect,
      country_id: this.form1.country_id,
      min_price:  this.form1.min_price,
      max_price:  this.form1.max_price,
      ip:         this.ipAddress,
      radius:     this.form1.radius1,
    }

    // ——— NEW: include area if set ———
    const area = this.form1.area
      ? `&area=${encodeURIComponent(this.form1.area)}`
      : ''
    

    if (dealselect === 5) {
      // video path unchanged…
      const localObject = JSON.parse(localStorage.getItem('catSearchedValues')) || {}
      const queryParams = {
        subcat_id: localObject.selectSubCategoty || '',
        city_id:   localObject.selectCity      || city_id,
      }
      axios.get('/api/video/all', { params: queryParams })
           .then(resp => {
             this.videos = resp.data.videos
             this.dealtype_selected = 5
             this.form1.area   = selectedArea;
        this.areaSearch   = selectedArea;
           })
           
           .catch(console.error)
           this.preserveArea()
           if (popup) this.dialog2 = false
    }
    else {
      // append &area if present
     
      const url =
        `/api/filter/data?` +
        `type=${dealselect}` +
        `&subcat_id=${subcat_id}` +
        `&job_cat_id=${jobcat_id}` +
        `&city_id=${city_id}` +
        `&country_id=${fdata.country_id}` +
        `&min_price=${fdata.min_price}` +
        `&max_price=${fdata.max_price}` +
        `&radius=${fdata.radius}` +
        `&ip=${fdata.ip}` +
        `&searchy=${encodeURIComponent(ssearch)}` +
        area

      axios.get(url, config)
           .then(resp => {
            console.log('[SearchPage]  → filter/data response:', resp.data);
            this.preserveArea()
             this.maxprice = resp.data.maxPrice
             switch (resp.data.searchType) {
               case 0:
                 this.hotds = resp.data.filter_data
                 this.dealtype_selected = 0
                 break
               case 1:
                 this.salesd = resp.data.filter_data
                 this.dealtype_selected = 1
                 break
               case 2:
                 this.jobsd = resp.data.filter_data
                 this.dealtype_selected = 2
                 break
               case 3:
                 this.allists = resp.data.filter_data
                 this.dealtype_selected = 3
                 break
             }
             this.form1.area   = selectedArea;
             this.areaSearch   = selectedArea;
             if (popup) this.dialog2 = false
           })
           .catch(console.error)
           console.error('[SearchPage]  × filter/data error:', err);
    }


    //featured business always fires
   if (city_id) {
    axios.get(`/api/business/featured?city_id=${city_id}`)
      .then(resp => {
        this.features = resp.data.businessFeature
        this.resetCarousel()
      })
      .catch(console.error)
  }
  },
      
        async fetchAreas() {
            if (!this.form1.city_id) {
                this.areas = []
                return
            }
            const cityObj = this.locs.find(l => l.city_id === this.form1.city_id)
            if (!cityObj) { this.areas = []; return }
            const cityName = cityObj.city

            try {
                const { data } = await axios.get('https://nominatim.openstreetmap.org/search', {
                params: { q: cityName, format: 'json', addressdetails: 1, limit: 20 }
                })
                this.areas = data
                .map(place => {
                    const a = place.address
                    return (a.neighbourhood || a.suburb || a.city_district || a.village) 
                    ? { name: a.neighbourhood || a.suburb || a.city_district || a.village } 
                    : null
                })
                .filter(x => x)
                .filter((x,i,arr) => arr.findIndex(y=>y.name===x.name)===i)
            } catch {
                this.areas = []
            }
            },
//             async areaKeyUp(val) {
//   this.areaSearch = val
//   if (val.length < 3 || !this.form1.city_id) {
//     this.areas = []
//     return
//   }
//   this.loadingAreas = true
//   try {
//     const city = this.locs.find(l=>l.city_id===this.form1.city_id)?.city || ''
//     const { data } = await axios.get('https://nominatim.openstreetmap.org/search', {
//       params: { q:`${val}, ${city}`, format:'json', limit:20, addressdetails:1 }
//     })
//     this.areas = data.map(p => ({ name: p.display_name }))  // or your mapping
//   } catch {
//     this.areas = []
//   } finally {
//     this.loadingAreas = false
//   }
// },
      
async areaKeyUp(val) {
      this.areaSearch = val
      if (val.length < 3 || !this.form1.city_id) {
        this.areas = []
        return
      }
      this.loadingAreas = true
      try {
        const city = this.locs.find(l => l.city_id === this.form1.city_id)?.city || ''
        const { data } = await axios.get('https://nominatim.openstreetmap.org/search', {
          params: { q: `${val}, ${city}`, format:'json', limit:20, addressdetails:1 }
        })
        this.areas = data.map(p => ({ name: p.display_name }))
      } catch {
        this.areas = []
      } finally {
        this.loadingAreas = false
      }
    },
    onAreaChange(val) {
    // whenever v-model (form1.area) changes…
    if (!val) {
      // user cleared it → wipe out the list
      this.areas = []
      this.areaSearch = ''
    }
  },
  onAreaClear() {
    // when the little “×” is clicked
    this.areas = []
    this.areaSearch = ''
    this.form1.area = ''
  },
    onAreaSelect(val) {
      // Put the selected value back into the search box so it stays visible
      this.areaSearch = val
      // And keep form1.area = val so your filters pick it up
      this.form1.area  = val
    },
onMobile(){
            this.dialog2 = true;
        },
        resetFilter(){
            this.resetform1();
        },
        resetform1(){
            let dealtype = 3;
            this.form1.country_id = 1;
            this.form1.subcat_id = '';
            this.form1.city_id = '';
            this.form1.min_price ='';
            this.form1.max_price = '';
            this.form1.dealtype_selected = 3;
            this.form1.radius1 = '';
            this.form1.searchy = '';
           this.form1.area = '';
            this.areaSearch = '';
            localStorage.setItem('jobcat_id',null);
            this.$store.dispatch('setdealtypeid',{dealtype});
            localStorage.setItem('subcat_id',null);
            localStorage.setItem('city_id',null);
        },
        getCurrentURL() {
            return window.location.href;
        },
        resetCarousel() {
            // Increment carouselKey to force re-rendering
            this.carouselKey++;
        },
    },
    mounted: function () {
        const that = this;
        jquery.ajax({
            url: 'https://api.ipify.org?format=json',
            success: function(res) {
                that.ipAddress = res.ip;
            }
        });
        
        this.getNewdata();
    },
    computed: {
        slist() {
            return this.salesd.slice((this.page - 1) * this.perPage,this.page * this.perPage)
        },
        hlist() {
            return this.hotds.slice((this.page - 1) * this.perPage, this.page * this.perPage)
        },
        jlist() {
            return this.jobsd.slice((this.page - 1) * this.perPage, this.page * this.perPage)
        },
        alist() {
            return this.allists.slice((this.page - 1) * this.perPage, this.page * this.perPage)
        },
        vlist() {
            return this.videos.slice((this.page - 1) * this.perPage, this.page * this.perPage)
        }
    },
}
</script>

<style scoped>
.mobile a {text-decoration: none;}
.mds {
    min-width: 40px;
    max-width: 40px;
    text-align: center;
    margin-right: 5px;
    }
.item.mitem {
    box-shadow: 0 6px 10px -6px rgba(0,0,0,0.5);
    background: #f1f2f380;
    padding: 7px;
    }
.jobcard{
    margin-bottom: 20px !important;
    padding-left: 30px !important;
    }
.jobrow{
    overflow-x:hidden;
    }
.videorow{
overflow-x:hidden;
}
.mimg {
    width: 110px;
    }
.mtitle {
    font-weight: 600;
    font-size: 12.25px;
    line-height: 14.7px;
    display: block;
    max-height: 29.4px;
    overflow: hidden;
    margin-bottom: 0.10rem;
    }
.mdetails {
    width: 100%;
    padding: 7px 0 0 7px;
    }
.mcontact {
    width: 42px;
    }
.catside{
    background: #e5e5e5;
    padding: 10px;
    }
.sec10{
    background: #f2f2f2;
    padding: 5px 15px;
    margin-bottom: 10px;
    }
.sec2{
    background: #f2f2f2;
    padding: 5px 15px;
    margin-bottom: 20px;
    }
.sec1{
    background: #f2f2f2;
    padding: 5px 15px;
    margin-bottom: 40px;
    }
.dtitle{
    font-size: 24px;
    line-height: 29px;
    max-height:58px;
    overflow: hidden;
    display: block;
    margin-bottom: 0;
    }
.dname{font-size: 12px;line-height: 18px;max-height: 18px;overflow: hidden;display: block;margin-bottom: 0;}
.sel14, .sel14 label{
    font-size: 14px;
    }
.v-menu__content.theme--light.v-menu__content--fixed.v-autocomplete__content {
    max-height: 200px !important;
    }
.v-menu__content.theme--light.v-menu__content--fixed.menuable__content__active.v-autocomplete__content {
    max-height: 200px !important;
    }
.feat.badge.bg-yell.text-black {
    background: #ff0;
    position: absolute;
    top: 0;
    font-weight: 400;
    }
div.loc {font-size: 12px}
.adtitle {
    font-weight: 600;
    font-size: 13px;
    }
@media (min-width: 991.9px){
    .ipad {display: none !important}
    }
@media (max-width: 991.9px){
    .m992 {display: none !important;}
    }
@media screen and (max-width: 768px) {
.videorow {
max-width: 100%;
margin: 0 auto;
padding: 0 10px;
}

.videorow .v-card {

padding: 10px;
}

.videorow .mtitle {
font-size: 1rem;
font-weight: 600;
line-height: 1.2;
word-wrap: break-word; /* Prevent text overflow */
}
.videorow .mreviews .badge {
font-size: 0.8rem;
background-color: #333;
color: #fff;
}
.videorow v-img {
height: 80px !important;
width: 80px !important;
}

.videorow .address, .videorow .cred {
font-size: 0.85rem; /* Reduce font size for readability */
}

.d-flex.flex-column.col.col-8 {
padding-left: 16px !important;
}

.video-contant{
    align-items: center;
}

}
</style>