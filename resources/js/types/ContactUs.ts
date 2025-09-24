export interface MessageInterFace {
    id?: number,
    name: string,
    email: string,
    subject: string,
    message: string,
    is_read: boolean,
    is_replied: boolean,
    reply?: string|null,
    ip: string|null,
    created_at?: string,
    updated_at?: string,
}
