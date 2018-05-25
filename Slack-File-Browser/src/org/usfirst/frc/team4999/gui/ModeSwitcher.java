package org.usfirst.frc.team4999.gui;

import java.awt.Color;
import java.awt.Dimension;
import java.awt.Graphics;
import java.awt.Graphics2D;
import java.awt.GridBagConstraints;
import java.awt.GridBagLayout;
import java.awt.Rectangle;
import java.awt.event.MouseListener;
import java.awt.geom.Path2D;

import javax.swing.JPanel;

public class ModeSwitcher extends JPanel {
	
	private class Tab extends JPanel {
		private String label;
		
		private Path2D.Double tabShape;
		
		private int width = 20, height = 70, angle = 10;
		
		public Tab(String label, MouseListener clickListener) {
			this.label = label;
			addMouseListener(clickListener);
			
			setPreferredSize(new Dimension(width, height));
			
			tabShape = new Path2D.Double();
			tabShape.moveTo(width, height);
			tabShape.lineTo(0, height-angle);
			tabShape.lineTo(0, angle);
			tabShape.lineTo(width, 0);
		}
		
		@Override
		public void paintComponent(Graphics gd) {
			Graphics2D g = (Graphics2D) gd;
			
			g.setPaint(Color.GRAY);
			g.fill(new Rectangle(0,0,width,height));
			
			g.setPaint(Color.WHITE);
			g.fill(tabShape);
			
			g.setPaint(Color.BLACK);
			g.draw(tabShape);
			
			//TODO: Draw text
		}
	}
	
	private JPanel selected;
	
	private ChannelsComponent channels;
	private FilesComponent files;
	
	public ModeSwitcher() {
		channels = new ChannelsComponent();
		files = new FilesComponent();
		selected = files;
		
		setLayout(new GridBagLayout());
		GridBagConstraints c = new GridBagConstraints();
		
		c.gridx = 0;
		c.gridy = 0;
		
		add(new Tab("Test", null));
		
	}
	
	public JPanel getSelected() {
		return selected;
	}

}
