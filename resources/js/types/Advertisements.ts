export enum statusEnum {
  PENDING = 'pending',
  APPROVED = 'approved',
  REJECTED = 'rejected',
}

export interface Advertisement {
  id: number;
  name: string;
  description: string;
  slug: string;
  price: number;
  reject_reason?: string;
  category: {
    id?: number;
    name?: string;
    slug?: string;
  };
  sub_category: {
    id?: number;
    name?: string;
    slug?: string;
  };
  sub_sub_category?: {
    id?: number;
    name?: string;
    slug?: string;
  };
  main_media: {
    url?: string;
    url_thumb?: string;
  };
  media?: {
    id: number;
    is_main: boolean;
  }[];
  user: {
    id?: number;
    name?: string;
    avatar_url?: string;
    country_code: string;
    phone_number: string;
    deleted_at?: string | null;
    is_verified?: boolean;
    is_business?: boolean;
    slug?: string;
    email?: string;
  };
  country: {
    id?: number;
    name?: string;
  };
  city: {
    id?: number;
    name?: string;
  };
  comments_count: number;
  is_favorite: boolean;
  filters: {
    name: string;
    value: string;
  }[];
  status?: statusEnum;
  is_owner?: boolean;
  latitude: number | null | string;
  longitude: number | null | string;
  views?: number;
  post_as_story?: boolean;
  created_at: string;
  deleted_at?: string | null;
}
