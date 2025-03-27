<a href="{{ $blogLink }}" title="{{ $blogTitle }}">
    <img class="w-full h-[250px] xl:h-[300px] object-cover rounded-[32px] pointer-events-none select-none" src="{{ asset($blogImgSrc) }}" alt="Blog">
    <p style="--truncate-line: 2;" class="mt-5 text-2xl text-secondary font-semibold custom-truncate">{{ $blogTitle }}</p>
    <p style="--truncate-line: 4;" class="mt-3.5 text-secondary/50 custom-truncate">{{ $blogCaption }}</p>
</a>
