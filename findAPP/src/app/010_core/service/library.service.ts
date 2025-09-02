// src/app/library/library.service.ts
import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Library } from './library.model';

@Injectable({ providedIn: 'root' })
export class LibraryService {
  private http = inject(HttpClient);
  // ajuste conforme seu ambiente:
  private baseUrl = 'http://find/api/library';

  getById(id: number): Observable<Library> {
    return this.http.get<Library>(`${this.baseUrl}/${id}`);
  }

  list(search?: string): Observable<Library[]> {
    let params = new HttpParams();
    if (search) params = params.set('q', search);
    return this.http.get<Library[]>(this.baseUrl, { params });
  }

  create(data: Library): Observable<Library> {
    return this.http.post<Library>(this.baseUrl+'/create', data);
  }

  update(id: number, data: Library): Observable<Library> {
    return this.http.put<Library>(`${this.baseUrl}/save/${id}`, data);
  }

  // Opcional: upload do logo (backend deve devolver uma URL/caminho salvo)
  uploadLogo(file: File): Observable<{ path: string }> {
    const form = new FormData();
    form.append('file', file);
    return this.http.post<{ path: string }>('/api/upload', form);
  }
}
