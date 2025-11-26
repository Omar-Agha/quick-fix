// Generic pagination data interface
export interface PaginationData {
    current_page: number;
    per_page: number;
    total: number;
    last_page: number;
}

// Generic CRUD page props interface
export interface CrudPageProps<T> {
    data: T[];
    pagination?: PaginationData;
    view?: 'grid' | 'table';
}

// Generic data response interface for API calls
export interface DataResponse<T> {
    data: T[];
    pagination: PaginationData;
}

// Re-export main types for convenience

