import { Injectable } from '@angular/core';

@Injectable({
  providedIn: 'root',
})
export class LocalStorageService {
  private storage: Storage;
  constructor() {
    this.storage = window.localStorage;
  }

  set(key: string, value: any): boolean {
    if (this.storage) {
      this.storage.setItem(key, value);
      return true;
    }
    return false;
  }

  get(key: string): any {
    if (!this.storage) {
      return null;
    }

    const raw = this.storage.getItem(key);
    if (raw === null || raw === undefined) {
      // nada salvo sob essa chave
      return null;
    }

    try {
      return JSON.parse(raw);
    } catch {
      // caso o valor não seja um JSON válido, retorna o texto cru
      return raw;
    }
  }

  check(key: string): boolean {
    if (this.storage) {
      return this.storage.getItem(key) !== null;
    }
    return false;
  }

  remove(key: string): boolean {
    if (this.storage) {
      this.storage.removeItem(key);
      return true;
    }
    return false;
  }

  clear(): boolean {
    if (this.storage) {
      this.storage.clear();
      return true;
    }
    return false;
  }
}
