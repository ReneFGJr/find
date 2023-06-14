import { ComponentFixture, TestBed } from '@angular/core/testing';

import { BooksInsertIsbnComponent } from './books-insert-isbn.component';

describe('BooksInsertIsbnComponent', () => {
  let component: BooksInsertIsbnComponent;
  let fixture: ComponentFixture<BooksInsertIsbnComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [BooksInsertIsbnComponent]
    });
    fixture = TestBed.createComponent(BooksInsertIsbnComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
