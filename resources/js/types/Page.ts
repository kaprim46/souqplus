export enum statusEnum {
    active = 'active',
    inactive = 'inactive'
}

export interface Page {
    id?: number;
    name: string;
    slug: string;
    content: string;
    status: statusEnum;
    icon: File | null;
    icon_url: string;
    created_at?: string;
    updated_at?: string;
}
