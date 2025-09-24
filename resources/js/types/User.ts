export interface User {
    id?: number;
    uuid: string;
    name: string;
    email: string;
    role: string;
    phone_number?: string;
    country_code?: string;
    is_verified?: boolean;
    verified_at?: string | null | Date;
    bio?: string;
    avatar?: File;
    avatar_url?: string;
    business_info?: BusinessInfo;
}

export interface BusinessInfo {
    //business_type, business_name, business_bio, business_logo, business_cover, phone_number, country_code, business_email, business_location
    business_type: string
    business_name: string
    business_district?: string
    business_bio?: string
    business_logo?: File;
    business_logo_url?: string
    cover_image?: File;
    business_cover_url?: string
    phone_number?: string
    country_code?: string
    cover_image_url?: string
    business_email?: string
    city?: {
        id: number
        name: string
    },
    country?: {
        id: number
        name: string
    }
    business_location?: {
        lat: number
        lng: number
    }
}

export interface FormRegister {
    name: string
    email: string
    password: string
    confirm_password: string
    country_code: string
    phone_number: string
    termAndCondition?: boolean
    role?: string
    country_id?: number
    city_id?: number
    business_info: BusinessInfo
}


export interface FormCustomer {
    is_verified: boolean | string
    name: string
    email: string
    password: string
    confirm_password: string
    country_code: string
    phone_number: string
    role: string
    uuid?: string
    id?: number
}

export interface CustomersList {
    is_verified: boolean
    country_code: string
    created_at: string
    deleted_at: string
    email: string
    id: number
    name: string
    phone_number: string
    role: string
    updated_at: string
    uuid: string
    advertisements_count?: number
}
