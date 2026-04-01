export type AuthUser = {
    id: number;
    name: string;
    email: string;
    role: string | null;
};

export type AuthPayload = {
    message?: string;
    token?: string;
    token_type?: string;
    redirect_url?: string | null;
    user?: AuthUser;
    authenticated?: boolean;
};
