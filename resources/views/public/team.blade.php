@extends('layouts.public')

@section('content')
<div class="flex-1 flex flex-col">
    <!-- Hero Section -->
    <section class="@container w-full">
        <div class="w-full relative h-[480px] lg:h-[520px] bg-cover bg-center flex flex-col items-center justify-center text-center px-4" data-alt="Group of diverse people working together in a kitchen environment, smiling" style='background-image: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.6)), url("https://lh3.googleusercontent.com/aida-public/AB6AXuDMw3TTXpSMKBsoz1w_UWC5ADYtsJDWBZ6TXRaAj07Y6J32iUpXAxYl1dnxOcFQ1Dh4sjblc9jMSD58H4_lyAc1eMhjQ3eWylcUNWiThjuRQHEety9znCV-w7RQGNlAaZ1U30D_UAp_Q8B9AdCdmZo25vzPv9h9wzOEx7Ae0b9JPLDPHf9ZGqXafXEg5zC3vznNdPXJBftX4nF3Zle-cX_vmMAHM5C_lBONVsPTAN6xyEtA_Dogcp5Kjkfc5Mleb6hvHHtY_BT2aShp");'>
            <div class="max-w-[800px] flex flex-col items-center gap-6 animate-fade-in-up">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/20 backdrop-blur-sm border border-primary/30 text-primary-light text-xs font-bold uppercase tracking-wider text-white">
                    <span class="material-symbols-outlined text-sm">groups</span> Our People
                </span>
                <h1 class="text-white text-4xl md:text-5xl lg:text-6xl font-black leading-tight tracking-tight drop-shadow-sm">
                    Nourishing Minds,<br class="hidden sm:block"/> One Meal at a Time
                </h1>
                <p class="text-gray-200 text-base md:text-lg lg:text-xl font-normal max-w-2xl leading-relaxed">
                    Meet the dedicated professionals, nutritionists, and logistics experts working behind the scenes to ensure every child gets a healthy start.
                </p>
                <div class="flex gap-4 mt-2">
                    <button class="flex items-center justify-center rounded-lg h-12 px-8 bg-primary text-[#181511] text-base font-bold hover:scale-105 transition-transform shadow-lg shadow-orange-900/20">
                        Join Our Mission
                    </button>
                    <button class="flex items-center justify-center rounded-lg h-12 px-8 bg-white/10 backdrop-blur-md border border-white/30 text-white text-base font-bold hover:bg-white/20 transition-all">
                        Volunteer
                    </button>
                </div>
            </div>
        </div>
    </section>

    <div class="w-full max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        <!-- Leadership Section -->
        <div class="mb-16">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 pb-4 border-b border-[#e6e2de] dark:border-[#3a3025]">
                <div>
                    <h2 class="text-[#181511] dark:text-white text-3xl font-bold leading-tight tracking-tight">Leadership</h2>
                    <p class="text-gray-500 dark:text-gray-400 mt-2">Guiding our vision for a hunger-free future.</p>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <!-- Card 1 -->
                <div class="group bg-white dark:bg-neutral-dark rounded-xl overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border border-[#e6e2de] dark:border-[#3a3025]">
                    <div class="relative h-64 overflow-hidden">
                        <img alt="Professional headshot of Sarah Jenkins, smiling woman in blazer" class="w-full h-full object-cover object-top group-hover:scale-105 transition-transform duration-500" data-alt="Professional headshot of Sarah Jenkins, smiling woman in blazer" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAIRGQXkWFmtM-nATxiJjAviVse1IQkKE4QXgkVQgKeh8VzXpIeB3f7e37-5nW_9-ZhIFMFKAjDiBRlI495yJLr7o5rk3oFoA8O8ATWdLJgbfag57AS42Hj_MuiFFHLIFvzKWVT1J8LpfrsxeXFIB5r2t79V-6NqwORDFmwQs5EQ3v-qNbNmWWdzvi9ew5emjgLK-3QWb9q84loT7sCmotCW-_-pY3Y4rYp-ibA2E6mn2yRycN90-cE2vaHN-lNYSA7cKiAO9-Lpesp"/>
                        <div class="absolute bottom-0 left-0 w-full h-1/2 bg-gradient-to-t from-black/60 to-transparent opacity-60"></div>
                    </div>
                    <div class="p-5 flex flex-col gap-2">
                        <div>
                            <h3 class="text-lg font-bold text-[#181511] dark:text-white">Sarah Jenkins</h3>
                            <span class="inline-block text-primary text-sm font-bold uppercase tracking-wide mt-1">Program Director</span>
                        </div>
                        <p class="text-gray-600 dark:text-gray-300 text-sm line-clamp-3 leading-relaxed">
                            Sarah brings over 15 years of non-profit management experience. She leads our strategic initiatives with a heart for community advocacy.
                        </p>
                        <div class="mt-4 pt-4 border-t border-[#f0ede9] dark:border-[#3a3025] flex gap-3">
                            <a class="text-gray-400 hover:text-primary transition-colors" href="#"><span class="material-symbols-outlined text-xl">mail</span></a>
                            <a class="text-gray-400 hover:text-[#0077b5] transition-colors" href="#"><span class="material-symbols-outlined text-xl">work</span></a>
                        </div>
                    </div>
                </div>
                <!-- Card 2 -->
                <div class="group bg-white dark:bg-neutral-dark rounded-xl overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border border-[#e6e2de] dark:border-[#3a3025]">
                    <div class="relative h-64 overflow-hidden">
                        <img alt="Professional headshot of David Chen, man in suit smiling" class="w-full h-full object-cover object-top group-hover:scale-105 transition-transform duration-500" data-alt="Professional headshot of David Chen, man in suit smiling" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAR65MlqJCxyapK7Qx6Kd75ahWnnZ8iDknTI0PokoV7tqmAvlOlrm4ev3RaiXFyyKVBR8DXdqVr3otIsnks8r_TTDIooUxoGYT1K17opmbVmGL87sBDYuDDzz1tAU2M-tpbl7hJMhVIoD0E9Qs-8Ur1P3PIeJRHZc8v8fAxwdXTby2YPDWlA6qiIEQ2vheaueCnkfuo0nBzwT0KciipWyWK0X2J9R8iI754PEVui81C5_1VjBoFw552GOotz8pe-5mWUuHs3dEBI88S"/>
                    </div>
                    <div class="p-5 flex flex-col gap-2">
                        <div>
                            <h3 class="text-lg font-bold text-[#181511] dark:text-white">David Chen</h3>
                            <span class="inline-block text-primary text-sm font-bold uppercase tracking-wide mt-1">Head Nutritionist</span>
                        </div>
                        <p class="text-gray-600 dark:text-gray-300 text-sm line-clamp-3 leading-relaxed">
                            Certified dietician focused on creating balanced, allergy-safe menus that kids actually love to eat.
                        </p>
                        <div class="mt-4 pt-4 border-t border-[#f0ede9] dark:border-[#3a3025] flex gap-3">
                            <a class="text-gray-400 hover:text-primary transition-colors" href="#"><span class="material-symbols-outlined text-xl">mail</span></a>
                            <a class="text-gray-400 hover:text-[#1DA1F2] transition-colors" href="#"><span class="material-symbols-outlined text-xl">rocket_launch</span></a>
                        </div>
                    </div>
                </div>
                <!-- Card 3 -->
                <div class="group bg-white dark:bg-neutral-dark rounded-xl overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border border-[#e6e2de] dark:border-[#3a3025]">
                    <div class="relative h-64 overflow-hidden">
                        <img alt="Professional headshot of Maria Rodriguez, smiling woman" class="w-full h-full object-cover object-top group-hover:scale-105 transition-transform duration-500" data-alt="Professional headshot of Maria Rodriguez, smiling woman" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBTuEwGb-AJnN9XVEHLvL5G1EOfQAtckjIrxBwx2cvZx4OwWWtO5ZLV979Vk1TuHwmp75fBU8FelMzxZCd9UK_2qCnEMib1zm_bnKVb3z_0ACfQ12Etj2ojkFlNFRCnuAcuTJfIMv4ZDFPWd1K1__o5nLS0nByUNe5ki-wKzCvG91QCq_nqubT-plRpS8pMr-lpUZKJQ6MrQgvtd73tQzr5-UFqY-PGTX1KhMaTwRskDlNpbX3hQ3EMxeAi9jnuqTeaXHAl-aqvmsYA"/>
                    </div>
                    <div class="p-5 flex flex-col gap-2">
                        <div>
                            <h3 class="text-lg font-bold text-[#181511] dark:text-white">Maria Rodriguez</h3>
                            <span class="inline-block text-primary text-sm font-bold uppercase tracking-wide mt-1">Operations Manager</span>
                        </div>
                        <p class="text-gray-600 dark:text-gray-300 text-sm line-clamp-3 leading-relaxed">
                            Maria ensures the logistics of food delivery run smoothly across all 50 partner schools in the district.
                        </p>
                        <div class="mt-4 pt-4 border-t border-[#f0ede9] dark:border-[#3a3025] flex gap-3">
                            <a class="text-gray-400 hover:text-primary transition-colors" href="#"><span class="material-symbols-outlined text-xl">mail</span></a>
                            <a class="text-gray-400 hover:text-[#0077b5] transition-colors" href="#"><span class="material-symbols-outlined text-xl">work</span></a>
                        </div>
                    </div>
                </div>
                <!-- Card 4 -->
                <div class="group bg-white dark:bg-neutral-dark rounded-xl overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border border-[#e6e2de] dark:border-[#3a3025]">
                    <div class="relative h-64 overflow-hidden">
                        <img alt="Professional headshot of James Wilson, man with glasses" class="w-full h-full object-cover object-top group-hover:scale-105 transition-transform duration-500" data-alt="Professional headshot of James Wilson, man with glasses" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCHq5B_nEJD2X5m21VcqB_Dhy6q3Vwa9xjV-mOwZb9wXBWynvShNkpAmOT-XG0bbUjtfn48KDdtyTlRnk1X4mb68f5-PeOr8fpGWBNorCmD5IkGznHodnsNEopUED3rUQUuDH1YNhqj8grNJeqCzduT0Wg_a3_Aj78RZbiXZfLFFSFbR94aEl_3bwtzwZiETm9wDcnMxT_7neKXII4S4n2h72n-Hh9ho8W4r1QHOJYZiuIXSPvVNwNS2iWoxCMsCROWlta0LUUiZHKA"/>
                    </div>
                    <div class="p-5 flex flex-col gap-2">
                        <div>
                            <h3 class="text-lg font-bold text-[#181511] dark:text-white">James Wilson</h3>
                            <span class="inline-block text-primary text-sm font-bold uppercase tracking-wide mt-1">Community Lead</span>
                        </div>
                        <p class="text-gray-600 dark:text-gray-300 text-sm line-clamp-3 leading-relaxed">
                            James connects local farmers and volunteers with our program to keep our sourcing sustainable and local.
                        </p>
                        <div class="mt-4 pt-4 border-t border-[#f0ede9] dark:border-[#3a3025] flex gap-3">
                            <a class="text-gray-400 hover:text-primary transition-colors" href="#"><span class="material-symbols-outlined text-xl">mail</span></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Operations Staff Section -->
        <div>
            <div class="mb-8 pb-4 border-b border-[#e6e2de] dark:border-[#3a3025]">
                <h2 class="text-[#181511] dark:text-white text-3xl font-bold leading-tight tracking-tight">Program Personnel</h2>
                <p class="text-gray-500 dark:text-gray-400 mt-2">The boots on the ground making it happen every day.</p>
            </div>
            <!-- Grid Layout for Personnel - More compact card style -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-5">
                <!-- Staff Card 1 -->
                <div class="flex flex-col items-center bg-white dark:bg-neutral-dark p-6 rounded-xl border border-[#e6e2de] dark:border-[#3a3025] text-center hover:shadow-md transition-shadow">
                    <div class="size-24 rounded-full overflow-hidden mb-4 border-4 border-primary/20">
                        <img alt="Portrait of Alice Cooper" class="w-full h-full object-cover" data-alt="Portrait of Alice Cooper" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDkbFORWdlErw3VwAKOB3zlsulgXSRLgkZZvQho2Q01to7o0cUVh-t53P0QlviqZjUIJ6hQL0hR-dgDWsvsoJv5t--VDdBnCrTfB4OvctlTaQjOheNqhQqgUylg6Vt0uWMxEsv-cEs31LAZaXmsIlgcPpuVORm3IRfL5l1gPpZUqHjXW9uzQOnAn1UyvoD9EHfBwQrI-zVJQp29s3NjmJp49igxgOV9cLYWPi7-2D6XOPccqC6vq3ZgNO3FUvYOZ8cJzksWFgM2YDXJ"/>
                    </div>
                    <h4 class="font-bold text-[#181511] dark:text-white text-base">Alice Cooper</h4>
                    <p class="text-xs font-semibold text-primary uppercase tracking-wide mb-2">Kitchen Manager</p>
                    <p class="text-gray-500 dark:text-gray-400 text-xs">Oversees daily meal prep quality.</p>
                </div>
                <!-- Staff Card 2 -->
                <div class="flex flex-col items-center bg-white dark:bg-neutral-dark p-6 rounded-xl border border-[#e6e2de] dark:border-[#3a3025] text-center hover:shadow-md transition-shadow">
                    <div class="size-24 rounded-full overflow-hidden mb-4 border-4 border-primary/20">
                        <img alt="Portrait of Marcus Johnson" class="w-full h-full object-cover" data-alt="Portrait of Marcus Johnson" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC9WoYv5fIN5MfhS4ftjG28CWNRPvojMGcN1Mrjm2aUy9uWhu1JaxvZi8BPEn-ez_kcjw7CgnmWszt637frM27QRVANGJqLdgPRvbRP7szJnPlED3J3Tqoa1zOerE3kKDZ-ae8zxLy6ZClJzAwa4o-9C3l0tOXoVW5hoRgaSltGOdi0PLbb4eMTGbZq17ZVNCNNdFCXzAeLUEndYSEUp1fyGZywXVeTsyRrUWZTH8GLt69uBcvrQwDYJ35R03FY2TxbVZm6mHDSHx17"/>
                    </div>
                    <h4 class="font-bold text-[#181511] dark:text-white text-base">Marcus Johnson</h4>
                    <p class="text-xs font-semibold text-primary uppercase tracking-wide mb-2">Logistics Coordinator</p>
                    <p class="text-gray-500 dark:text-gray-400 text-xs">Manages delivery routes and fleet.</p>
                </div>
                <!-- Staff Card 3 -->
                <div class="flex flex-col items-center bg-white dark:bg-neutral-dark p-6 rounded-xl border border-[#e6e2de] dark:border-[#3a3025] text-center hover:shadow-md transition-shadow">
                    <div class="size-24 rounded-full overflow-hidden mb-4 border-4 border-primary/20">
                        <img alt="Portrait of Elena Gomez" class="w-full h-full object-cover" data-alt="Portrait of Elena Gomez" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCWPfe0fstHkzQdGa-gI4z8UqQnIYXgsk8gCEzsstaxkfXkgjBdUNQINArsqcdII6QTvV3GMMbRIVOs91EYOcfiTsH3VXz8FXbPOo9bBFOok_wOozArwVZEieeUzfw7TkMD4zydio8_dD0YdARQNCfcoA_XUZgZUsN9wE6wpVDdTWgBWa4wHhtz4n8feisg8SdMv0YC-4uZAwOmlnJUlA2W-3Y8GXExZVzo-nvhKxkjps4bn1T4-l3nBQI4eRIV2BApvFArH8YHAjVX"/>
                    </div>
                    <h4 class="font-bold text-[#181511] dark:text-white text-base">Elena Gomez</h4>
                    <p class="text-xs font-semibold text-primary uppercase tracking-wide mb-2">Volunteer Lead</p>
                    <p class="text-gray-500 dark:text-gray-400 text-xs">Coordinates 200+ monthly volunteers.</p>
                </div>
                <!-- Staff Card 4 -->
                <div class="flex flex-col items-center bg-white dark:bg-neutral-dark p-6 rounded-xl border border-[#e6e2de] dark:border-[#3a3025] text-center hover:shadow-md transition-shadow">
                    <div class="size-24 rounded-full overflow-hidden mb-4 border-4 border-primary/20">
                        <img alt="Portrait of Tom Baker" class="w-full h-full object-cover" data-alt="Portrait of Tom Baker" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCnSfDyldl0GkFKE6bCXpcbLF5Iv-QWyeUfaw98zhLUanjvKmFm5VQOV4GRryiMWbubbJ0v4GuKL-Nxz-Vrh2zHkMnQjBDhgnV-D6NBiKG4tg0VKm4Quz49k1zPU-q3O7OHZ35fiPLnslTQzqv6LFSmBUohQ6xKTPy2ainvIM_wVexN8f5k0BBLqJ0re65B4B8OC0W3vjG1JjQPUiyZePFnjLc4oleEfGIntgzei15Q2cGJdpbztOjasHSWskOKgkB0f7AM7pV7sNYo"/>
                    </div>
                    <h4 class="font-bold text-[#181511] dark:text-white text-base">Tom Baker</h4>
                    <p class="text-xs font-semibold text-primary uppercase tracking-wide mb-2">Food Safety Officer</p>
                    <p class="text-gray-500 dark:text-gray-400 text-xs">Ensures compliance with health codes.</p>
                </div>
                <!-- Staff Card 5 -->
                <div class="flex flex-col items-center bg-white dark:bg-neutral-dark p-6 rounded-xl border border-[#e6e2de] dark:border-[#3a3025] text-center hover:shadow-md transition-shadow">
                    <div class="size-24 rounded-full overflow-hidden mb-4 border-4 border-primary/20">
                        <img alt="Portrait of Priya Patel" class="w-full h-full object-cover" data-alt="Portrait of Priya Patel" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCeZGo_USOXpxys7g4UPv2vp_wgye1PcfEHxIFmMv5QgCk43t3GoxQEHcm6c1uuhmFQNKcvqGYUi7U8o-YLedcCVob0E0JGXtUDzT0EacZ3WLyisPXPUEl-tUmCTpPBuA5JTFrBpUQVde4rdIfuAIPNS09s4eIJ9mX8orKANVH0KudWkzPs1hHw1o_Oo-pAONMRjxilKovRuhRO8G8B3eUYCufsiwfH0lUzu-jfHRqGRdVFVTwS6heMHLp4Kzwt4_u4KOMxT9LW5Ljv"/>
                    </div>
                    <h4 class="font-bold text-[#181511] dark:text-white text-base">Priya Patel</h4>
                    <p class="text-xs font-semibold text-primary uppercase tracking-wide mb-2">Dietary Aid</p>
                    <p class="text-gray-500 dark:text-gray-400 text-xs">Assists in menu planning.</p>
                </div>
            </div>
        </div>

        <!-- Join The Team CTA -->
        <div class="mt-16 rounded-2xl bg-gradient-to-r from-[#221a10] to-[#3a3025] dark:from-[#3a3025] dark:to-[#4e4033] p-8 md:p-12 relative overflow-hidden">
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="flex-1 text-center md:text-left">
                    <h3 class="text-2xl md:text-3xl font-bold text-white mb-3">Want to make a difference?</h3>
                    <p class="text-gray-300 text-lg">We are always looking for passionate individuals to join our kitchen, logistics, and admin teams.</p>
                </div>
                <div class="flex gap-4">
                    <button class="whitespace-nowrap rounded-lg bg-primary text-[#181511] px-6 py-3 font-bold text-sm md:text-base hover:bg-orange-400 transition-colors shadow-lg shadow-orange-500/20">
                        View Open Positions
                    </button>
                    <button class="whitespace-nowrap rounded-lg bg-transparent border border-white/20 text-white px-6 py-3 font-bold text-sm md:text-base hover:bg-white/10 transition-colors">
                        Become a Volunteer
                    </button>
                </div>
            </div>
            <!-- Decorative bg element -->
            <div class="absolute right-0 top-0 h-full w-1/3 bg-primary opacity-10 blur-3xl -mr-20 transform skew-x-12"></div>
        </div>
    </div>
</div>
@endsection
