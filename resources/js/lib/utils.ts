import { InertiaLinkProps } from '@inertiajs/vue3';
import { clsx, type ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';
import { h } from 'vue';

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
        else if (value !== undefined && value !== null) {
            formData.append(formKey, value as any);
        }
    }

    return formData;
}