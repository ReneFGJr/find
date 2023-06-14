import { ComponentFixture, TestBed } from '@angular/core/testing';

import { IsbnComponent } from './isbn.component';

describe('IsbnComponent', () => {
  let component: IsbnComponent;
  let fixture: ComponentFixture<IsbnComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [IsbnComponent]
    });
    fixture = TestBed.createComponent(IsbnComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
