import { AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import Avatar from "@/components/ui/avatar/Avatar.vue";
import { h } from "vue";


export function avatar(avatar: string, name: string) {
    return h('div', { class: 'flex items-center' }, [
        h(
            Avatar,
            { class: 'h-12 w-12' },
            {
                default: () => [
                    avatar
                        ? h(AvatarImage, { src: avatar, alt: avatar })
                        : null,
                    h(AvatarFallback, { class: 'bg-muted text-muted-foreground' }, () =>
                        name.charAt(0).toUpperCase()
                    ),
                ],
            }
        ),
    ]);
}

export function banner(src: string | undefined, title?: string) {
    return h(
        'div',
        { class: 'flex items-center' },
        [
            h(
                'div',
                {
                    class:
                        // 16:9 banner box
                        'relative w-40 aspect-[16/9] overflow-hidden rounded-lg bg-muted',
                },
                [
                    src
                        ? h('img', {
                            src,
                            alt: title ?? 'Banner',
                            class:
                                'h-full w-full object-cover transition-transform duration-300 hover:scale-105',
                        })
                        : h(
                            'div',
                            {
                                class:
                                    'flex h-full w-full items-center justify-center text-xs text-muted-foreground',
                            },
                            title ? `No banner for ${title}` : 'No banner'
                        ),
                ]
            ),
        ]
    );
}