export interface DataResponse<T> {
    data: T[];
    pagination: PaginationData;
}

// Generic pagination data interface
interface PaginationData {
    current_page: number;
    per_page: number;
    total: number;
    last_page: number;
}


export interface PaginationResponse<T> {
    data: T[],
    pagination: PaginationData
}
