import { InertiaLinkProps, router, usePage } from '@inertiajs/vue3';
import { clsx, type ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';
import { h } from 'vue';
import { toast } from 'vue-sonner';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export function urlIsActive(
    urlToCheck: NonNullable<InertiaLinkProps['href']>,
    currentUrl: string,
) {
    return toUrl(urlToCheck) === currentUrl;
}

export function toUrl(href: NonNullable<InertiaLinkProps['href']>) {
    return typeof href === 'string' ? href : href?.url;
}

export function renderErrorList(errors: Record<string, any>) {
    const items = Object.entries(errors).map(([field, message]) => {
        const prettyField = field.replace(/_/g, " ");
        const capitalized = prettyField.charAt(0).toUpperCase() + prettyField.slice(1);
        const msg = typeof message === "string" ? message : JSON.stringify(message);

        return h("li", [
            h("strong", `${capitalized}: `),
            msg
        ]);
    });

    return h(
        "div",
        { class: "font-medium" },
        [
            h(
                "ul",
                { class: "list-disc ml-5" },
                items
            )
        ]
    );
}

export function objectToFormData(obj: any, formData = new FormData(), parentKey = "") {

    for (const [key, value] of Object.entries(obj)) {
        const formKey = parentKey ? `${parentKey}[${key}]` : key;

        if (value instanceof File) {
            formData.append(formKey, value);
        }
        else if (value instanceof Blob) {
            formData.append(formKey, value);
        }
        else if (Array.isArray(value)) {
            value.forEach((v, i) => {
                objectToFormData(v, formData, `${formKey}[${i}]`);
            });
        }
        else if (typeof value === "object" && value !== null) {
            objectToFormData(value, formData, formKey);
        }
        else if (typeof value === "boolean") {
            formData.append(formKey, value ? "1" : "0");
        }
        else if (value !== undefined && value !== null) {
            formData.append(formKey, value as any);
        }
    }

    return formData;
}



export function saveRecord(
    url: string,
    data: any,
    isFormData: boolean,
    id?: number,
    showLoader?: () => void,
    hideLoader?: () => void,
    onSuccess?: () => void,
    onError?: (ex: any) => void,
    onFinish?: () => void,
) {
    showLoader?.();
    console.log(data);

    if (isFormData) {
        data = objectToFormData(data);
    }

    if (id) {

        data.append('id', id?.toString() || '');
    }

    router.post(url, data, {
        forceFormData: true,
        onSuccess: () => {
            toast.success('Service created successfully!');
            onSuccess?.();
        },
        onError: (ex) => {


            toast.error(renderErrorList(ex));
            onError?.(ex);
        },
        onFinish: () => {
            onFinish?.();
            hideLoader?.();
        },
    });
}
type SharedAppConfig = {
    app_name: string;
    locale: string;
    currency: string;
};

export function AppName(): string {
    const page = usePage<{ app_config?: SharedAppConfig }>();

    return page.props.app_config?.app_name ?? (import.meta.env.VITE_APP_NAME as string) ?? 'Laravel';
}

export function currencyFormat(amount: number): string {
    const page = usePage<{ app_config?: SharedAppConfig }>();
    const config = page.props.app_config;

    if (!config?.locale || !config?.currency) {
        return String(amount);
    }

    return new Intl.NumberFormat(config.locale, {
        style: 'currency',
        currency: config.currency,
    }).format(amount);
}

type MobileApplicationLinks = {
    android_link: string;
    ios_link: string;
};

function isValidStoreUrl(url: string | undefined): boolean {
    return Boolean(url && url !== '#' && url.startsWith('http'));
}

export function getDownloadAppLink(): string {
    const ua = navigator.userAgent;
    const page = usePage<{ mobile_application_links?: MobileApplicationLinks }>();
    const links = page.props.mobile_application_links;

    if (!isValidStoreUrl(links?.android_link) || !isValidStoreUrl(links?.ios_link)) {
        return '/download';
    }

    if (/android/i.test(ua)) {
        return links!.android_link;
    } else if (/iPhone|iPad|iPod/i.test(ua)) {
        return links!.ios_link;
    }

    return '/download';
}
export function downloadApp(): void {
    window.location.href = getDownloadAppLink();


}