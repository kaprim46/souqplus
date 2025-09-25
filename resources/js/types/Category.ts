import {Filter} from "@/stores/FilterStore";

export enum statusEnum {
    active = 'active',
    inactive = 'inactive'
}

export interface Category {
    id?: number;
    name: string;
    slug?: string;
    description: string;
    status: statusEnum;
    meta_title: string;
    meta_description: string;
    meta_keywords: string;
    meta_robots: string;
    category_id?: number|null;
    category?: {
        id: number;
        name: string;
    },
    filters: Filter[]
}


export interface SubSubCategory {
  id?: number;
  name: string;
  sub_category_id: number | null;
  status: 'active' | 'inactive';
  sub_category?: {
    id: number;
    name: string;
  }
}