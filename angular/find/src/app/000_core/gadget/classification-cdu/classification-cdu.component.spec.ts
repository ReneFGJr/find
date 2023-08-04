import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ClassificationCduComponent } from './classification-cdu.component';

describe('ClassificationCduComponent', () => {
  let component: ClassificationCduComponent;
  let fixture: ComponentFixture<ClassificationCduComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [ClassificationCduComponent]
    });
    fixture = TestBed.createComponent(ClassificationCduComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
