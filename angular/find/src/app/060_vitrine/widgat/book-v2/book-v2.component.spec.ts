import { ComponentFixture, TestBed } from '@angular/core/testing';

import { BookV2Component } from './book-v2.component';

describe('BookV2Component', () => {
  let component: BookV2Component;
  let fixture: ComponentFixture<BookV2Component>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [BookV2Component]
    });
    fixture = TestBed.createComponent(BookV2Component);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
