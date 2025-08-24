   <div class="container mx-auto max-w-[85rem] w-full">
       <div class="mt-10">
           <x-product-sections title="Feature Product" :products="$feature_products" :url="route('product-catalog')" />
           <x-featured-icon />
           <x-product-sections title="Latest Products" :products="$latest_products" :url="route('product-catalog')" />
       </div>
   </div>
